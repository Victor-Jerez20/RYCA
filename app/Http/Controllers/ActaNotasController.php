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
        $sedes = Sede::all();
        return view('acta.formulario', compact('carreras', 'sedes'));
    }

    public function procesar(Request $request)
    {
        $request->validate(['pdf' => 'required|mimes:pdf']);

        $carreraSeleccionada = $request->input('carrera');
        $cicloSeleccionado = $request->input('ciclo');
        $sedeSeleccionada = $request->input('sede');

        session([
            'carrera_seleccionada' => $carreraSeleccionada,
            'ciclo_seleccionado' => $cicloSeleccionado,
            'sede_seleccionada' => $sedeSeleccionada
        ]);

        $archivo = $request->file('pdf');
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($archivo->getRealPath());
        $texto = $pdf->getText();

        $lineas = explode("\n", $texto);

        $cursos = [];
        $cursoActual = null;

        foreach ($lineas as $index => $linea) {
            $linea = trim($linea);

            // Detectar encabezado de curso
            if (stripos($linea, 'Curso:') !== false) {
                $nombreCurso = trim(substr($linea, strpos($linea, 'Curso:') + 6)) ?? 'Curso desconocido';

                // Buscar metadatos en las siguientes líneas (hasta 5 líneas abajo)
                $anio = null;
                $semestre = null;
                $codigoCarrera = null;
                $sede = null;

                for ($i = 1; $i <= 5; $i++) {
                    $lineaSiguiente = $lineas[$index + $i] ?? '';

                    if (!$anio && preg_match('/Afro:\s*(\d{4})/i', $lineaSiguiente, $mAnio)) {
                        $anio = $mAnio[1];
                    }

                    if (!$semestre) {
                        if (str_contains($lineaSiguiente, '1:E')) $semestre = 1;
                        elseif (str_contains($lineaSiguiente, '2:I')) $semestre = 2;
                    }

                    if (!$codigoCarrera && preg_match('/cod\s*carrera:\s*(\d+)/i', $lineaSiguiente, $mCarrera)) {
                        $codigoCarrera = $mCarrera[1];
                    }

                    if (!$sede && preg_match('/Cod\s* sede:\s*(\d+)/i', $lineaSiguiente, $mSede)) {
                        $sede = $mSede[1];
                    }
                }

                $cursoActual = [
                    'nombre' => $nombreCurso,
                    'anio' => $anio ?? 'Desconocido',
                    'semestre' => $semestre ?? null,
                    'codigo_carrera' => $codigoCarrera ?? null,
                    'sede' => $sede ?? null,
                    'estudiantes' => [],
                ];

                $cursos[] = $cursoActual;
                continue;
            }

            // Detectar líneas con carné precedido por número de orden
            if (preg_match('/\b(\d{7})\b/', $linea, $matchCarne)) {
                $carne = $matchCarne[1];

                preg_match_all('/(NSP|SDE|\d{1,3})/', $linea, $matchesNumeros);
                $valores = $matchesNumeros[1] ?? [];

                if (count($valores) >= 1 && $cursoActual !== null) {
                    $consolidado = end($valores);
                    $consolidado = is_numeric($consolidado) ? (float) $consolidado : 0;

                    $cursos[array_key_last($cursos)]['estudiantes'][] = [
                        'carne' => $carne,
                        'consolidado' => $consolidado,
                    ];
                }
            }
        }

        // Eliminar cursos vacíos
        $cursos = array_filter($cursos, fn($c) => count($c['estudiantes']) > 0);

        if (empty($cursos)) {
            return response("No se encontraron cursos válidos.", 200);
        }

        session(['cursos_extraidos' => $cursos]);
        return view('acta.preview', compact('cursos'));
    }

    public function guardar(Request $request)
    {
        //dump($request->input('cursos'));
        //dd($request->input('cursos')[0]); // Muestra solo el primer curso para revisión

        $dataCursos = $request->input('cursos');

        if (!$dataCursos) {
            return redirect()->route('acta.formulario')->with('error', 'No hay datos para guardar.');
        }

        foreach ($dataCursos as $cursoInput) {
            $idCurso = $cursoInput['id_curso'];
            $idSede = $cursoInput['id_sede'];
            $fechaAprobacion = $cursoInput['anio'] . '-' . $cursoInput['mes'];
        

            if (!isset($cursoInput['estudiantes'])) continue;

            foreach ($cursoInput['estudiantes'] as $est) {
                \App\Models\Nota::create([
                    'carne' => $est['carne'],
                    'id_curso' => $idCurso,
                    'id_sede' => $idSede,
                    'consolidado' => $est['consolidado'],
                    'fecha_aprobacion' => $fechaAprobacion,
                ]);
            }
        }

        return redirect()->route('acta.formulario')->with('success', 'Notas guardadas correctamente.');
    }

}
