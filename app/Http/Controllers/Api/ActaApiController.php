<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class ActaApiController extends Controller
{
    public function preview(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf',
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

            foreach ($lineas as $lineaCruda) {
                $linea = trim($lineaCruda);
                if ($linea === '') {
                    continue;
                }

                if (!preg_match('/\b(\d{7,8})\b/', $linea, $matchCarne)) {
                    continue;
                }

                $carne = $matchCarne[1];

                $posCarne    = strpos($linea, $carne);
                $numerosFila = [];
                if ($posCarne !== false && $posCarne > 0) {
                    $prefix = substr($linea, 0, $posCarne);
                    if (preg_match_all('/\d{1,3}/', $prefix, $mFila)) {
                        $numerosFila = array_values(array_unique($mFila[0]));
                    }
                }

                $lineaSinCarne = str_replace($carne, str_repeat('X', strlen($carne)), $linea);

                $clean = preg_replace('/[^0-9A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+/u', ' ', $lineaSinCarne);
                $parts = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

                $nums = [];
                foreach ($parts as $p) {
                    if (preg_match('/^\d{1,3}$/', $p)) {
                        $nums[] = $p;
                    }
                }

                if (!empty($numerosFila) && !empty($nums)) {
                    $normFila = array_map(function ($n) {
                        return (string) ((int) $n);
                    }, $numerosFila);

                    $nums = array_values(array_filter($nums, function ($t) use ($normFila) {
                        return !in_array((string) ((int) $t), $normFila, true);
                    }));
                }

                $consolidado = !empty($nums) ? (int) end($nums) : 0;

                $estudiantes[] = [
                    'carne'       => $carne,
                    'consolidado' => $consolidado,
                ];
            }

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
                    'indice'         => $pageIndex,
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

        return response()->json([
            'ok'     => true,
            'cursos' => $paginasCursos,
        ]);
    }

    public function health()
    {
        return response()->json(['status' => 'ok']);
    }
}
