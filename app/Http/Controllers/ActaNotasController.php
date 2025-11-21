<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Nota;
use App\Models\Carrera;
use App\Models\Sede;

class ActaNotasController extends Controller
{
    public function formulario()
    {
        $carreras = Carrera::all();
        $sedes    = Sede::all();
        return view('acta.formulario', compact('carreras', 'sedes'));
    }

    public function procesar(Request $request)
    {
        $request->validate(['pdf' => 'required|mimes:pdf']);

        $carreraSeleccionada = $request->input('carrera');
        $cicloSeleccionado   = $request->input('ciclo');
        $sedeSeleccionada    = $request->input('sede');

        session([
            'carrera_seleccionada' => $carreraSeleccionada,
            'ciclo_seleccionado'   => $cicloSeleccionado,
            'sede_seleccionada'    => $sedeSeleccionada,
        ]);

        $archivo = $request->file('pdf');
        $parser  = new Parser();
        $pdf     = $parser->parseFile($archivo->getRealPath());

        $paginasCursos = [];

        foreach ($pdf->getPages() as $pageIndex => $page) {
            $textoPagina = $page->getText();
            $lineas      = preg_split("/\r\n|\n|\r/", $textoPagina);

            $nombreCurso    = 'Curso sin nombre';
            $anio           = null;
            $semestre       = null;
            $codigoCarrera  = null;
            $codCurso       = null;
            $seccion        = null;
            $codSedeHeader  = null;
            $estudiantes    = [];

            // Encabezado del curso (solo lee datos del curso)
            foreach ($lineas as $i => $lineaCruda) {
                $linea = trim($lineaCruda);
                if ($linea === '') {
                    continue;
                }

                if (stripos($linea, 'Curso:') !== false) {
                    $nombreCurso = trim(substr($linea, strpos($linea, 'Curso:') + 6)) ?: 'Curso sin nombre';

                    for ($j = 1; $j <= 8; $j++) {
                        $lineaSiguiente = $lineas[$i + $j] ?? '';

                        if (!$anio && preg_match('/Año:\s*(\d{4})/i', $lineaSiguiente, $mAnio)) {
                            $anio = $mAnio[1];
                        }
                        if (!$semestre && preg_match('/Semestre:\s*([12])/i', $lineaSiguiente, $mSem)) {
                            $semestre = (int) $mSem[1];
                        }
                        if (!$codCurso && preg_match('/Cod[_ ]curso:\s*(\d+)/i', $lineaSiguiente, $mCod)) {
                            $codCurso = $mCod[1];
                        }
                        if (!$codigoCarrera && preg_match('/Cod[_ ]Carrera:\s*(\d+)/i', $lineaSiguiente, $mCar)) {
                            $codigoCarrera = $mCar[1];
                        }
                        if (!$seccion && preg_match('/Sección:\s*(\d+)/i', $lineaSiguiente, $mSec)) {
                            $seccion = $mSec[1];
                        }
                        if (!$codSedeHeader && preg_match('/Cod[_ ]Sede:\s*(\d+)/i', $lineaSiguiente, $mSed)) {
                            $codSedeHeader = $mSed[1];
                        }
                    }

                    break;
                }
            }

            // Estudiantes (Carné + Consolidado) en ESTA página
            foreach ($lineas as $lineaCruda) {
                $linea = trim($lineaCruda);
                if ($linea === '') {
                    continue;
                }

                // Buscar carné (7 u 8 dígitos)
                if (!preg_match('/\b(\d{7,8})\b/', $linea, $matchCarne)) {
                    continue;
                }

                $carne = $matchCarne[1];

                // Números ANTES del carné = número(s) de fila -> hay que ignorarlos
                $posCarne = strpos($linea, $carne);
                $numerosFila = [];
                if ($posCarne !== false && $posCarne > 0) {
                    $prefix = substr($linea, 0, $posCarne);
                    preg_match_all('/\d{1,3}/', $prefix, $mFila);
                    $numerosFila = $mFila[0] ?? [];
                    $numerosFila = array_unique($numerosFila);
                }

                // Borrar el carné de la línea para no sacar trozos 210 / 128 / 4
                $lineaSinCarne = str_replace($carne, str_repeat('X', strlen($carne)), $linea);

                // Todos los números aislados de 1–3 dígitos
                preg_match_all('/(?<!\d)(\d{1,3})(?!\d)/', $lineaSinCarne, $matches);
                $tokens = $matches[1] ?? [];

                // Eliminar cualquier número que coincida con los de fila
                if (!empty($numerosFila) && !empty($tokens)) {
                    $normFila = array_map(function ($n) {
                        return (string) ((int) $n);
                    }, $numerosFila);

                    $tokens = array_filter($tokens, function ($t) use ($normFila) {
                        return !in_array((string) ((int) $t), $normFila, true);
                    });
                    $tokens = array_values($tokens);
                }

                // Consolidado = último número válido; si no hay, 0
                $consolidado = !empty($tokens) ? (int) end($tokens) : 0;

                $estudiantes[] = [
                    'carne'       => $carne,
                    'consolidado' => $consolidado,
                ];
            }

            // Dejar solo un registro por carné (última aparición gana)
            $map = [];
            foreach ($estudiantes as $est) {
                $c = $est['carne'] ?? null;
                if (!$c) {
                    continue;
                }
                $map[$c] = $est;
            }
            $estudiantes = array_values($map);

            if (!empty($estudiantes)) {
                $paginasCursos[] = [
                    'nombre'         => $nombreCurso,
                    'anio'           => $anio,
                    'semestre'       => $semestre,
                    'codigo_carrera' => $codigoCarrera,
                    'cod_curso'      => $codCurso,
                    'seccion'        => $seccion,
                    'sede'           => $codSedeHeader,
                    'estudiantes'    => $estudiantes,
                ];
            }
        }

        if (empty($paginasCursos)) {
            return response("No se encontraron cursos válidos en el PDF.", 200);
        }

        $cursos = array_values($paginasCursos);

        session(['cursos_extraidos' => $cursos]);

        return view('acta.preview', compact('cursos'));
    }

    public function guardar(Request $request)
    {
        $dataCursos = $request->input('cursos');

        if (!$dataCursos) {
            return redirect()->route('acta.formulario')->with('error', 'No hay datos para guardar.');
        }

        foreach ($dataCursos as $cursoInput) {
            $idCurso = $cursoInput['id_curso'] ?? null;
            $idSede  = $cursoInput['id_sede'] ?? null;
            $anio    = $cursoInput['anio'] ?? null;
            $mes     = $cursoInput['mes'] ?? null;

            if (!$idCurso || !$idSede || !$anio || !$mes) {
                continue;
            }

            $fechaAprobacion = sprintf('%04d-%02d-01', (int) $anio, (int) $mes);

            if (!isset($cursoInput['estudiantes']) || !is_array($cursoInput['estudiantes'])) {
                continue;
            }

            foreach ($cursoInput['estudiantes'] as $est) {
                $consolidado = isset($est['consolidado']) ? (int) $est['consolidado'] : 0;

                Nota::create([
                    'carne'            => $est['carne'],
                    'id_curso'         => $idCurso,
                    'id_sede'          => $idSede,
                    'fase_1'           => null,
                    'fase_2'           => null,
                    'fase_f'           => null,
                    'consolidado'      => $consolidado,
                    'fecha_aprobacion' => $fechaAprobacion,
                ]);
            }
        }

        return redirect()->route('acta.formulario')->with('success', 'Notas guardadas correctamente.');
    }
}
