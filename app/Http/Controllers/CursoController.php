<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotaRequest;
use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Empleado;
use App\Models\Estudiante;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use GuzzleHttp\Client;

class CursoController extends Controller
{
    public function index()
    {
        return Inertia::render('Cursos/Index', [
        ]);
    }

    public function indexEstudiante()
    {
        $usuario = User::find(auth()->id());
        $estudiante = Estudiante::where('persona_id', $usuario->persona_id)->first();

        return Inertia::render('Cursos/IndexEstudiante', [
            'estudiante' => $estudiante
        ]);
    }

    public function detalle($curso_id)
    {
        $curso = Curso::find($curso_id);

        return Inertia::render('Cursos/Detalle', [
            'curso' => $curso
        ]);
    }

    public function fetchDataTable()
    {
        $usuario = Auth::user();
        
        $empleado = Empleado::where('persona_id', $usuario->persona_id)->first();

        $cursos = Curso::with('grupo')->where('empleado_id', $empleado->id)->get();

        return compact('cursos');
    }

    public function fetchNotasByEstudiante($curso_id, $estudiante_id) {
        $array_estudiante = array();

        $nota = Nota::where('curso_id', $curso_id)->where('estudiante_id', $estudiante_id)->first();
        $curso = Curso::find($curso_id);
        $periodos = Periodo::where('nombre',  'LIKE', '%' . $curso->grupo->anio . '%')->get();
        
        $array_asistencias = array();

        foreach ($periodos as $key => $periodo) {
            $asistencias = Asistencia::join('clases', 'asistencias.clase_id', '=', 'clases.id')
                            ->where('estudiante_id', $estudiante_id)
                            ->where('clases.periodo_id', $periodo->id)
                            ->where('clases.curso_id', $curso_id)
                            ->select('tipo', 'observacion')
                            ->get();
            $array_asistencias[$key] = $asistencias;
        }

        $array_estudiante['estudiante_id'] = $estudiante_id;
        $array_estudiante['curso_id'] = $curso_id;

        if (is_null($nota)) {
            $array_nota = array();
            foreach ($periodos as $key2 => $periodo) {
                $array_nota['notas'][$key2] = '';
            }
            $array_nota['nota_final'] = '';

            $array_estudiante['nota'] = $array_nota;
        }
        else {
            $array_notas = explode(',', $nota->notas);
            
            $array_estudiante['nota']['notas'] = $array_notas;
            $array_estudiante['nota']['nota_final'] = $nota->nota_final;
        }

        $array_estudiante['asistencias'] = $array_asistencias;

        $estudiante = $array_estudiante;

        return compact('estudiante');
    }

    public function fetchGrupoEstudiantes($id)
    {
        $curso = Curso::find($id);

        $estudiantes = Estudiante::join('grupos_estudiantes', 'estudiantes.id', '=', 'grupos_estudiantes.estudiante_id')
        ->select('estudiantes.*')
        ->where('grupos_estudiantes.grupo_id', $curso->grupo_id)
        ->get();

        $listado_estudiantes = array();

        foreach ($estudiantes as $key => $estudiante) {
            $datos_nota = Nota::where('estudiante_id', $estudiante->id)->where('curso_id', $id)->first();

            $listado_estudiantes[$key]['curso_id'] = $id;
            $listado_estudiantes[$key]['estudiante_id'] = $estudiante->id;
            $listado_estudiantes[$key]['estudiante_nombre'] = $estudiante->persona->nombre_completo;
            $listado_estudiantes[$key]['nota_promedio'] = $datos_nota->nota_final;
            $listado_estudiantes[$key]['nota_prediccion'] = $datos_nota->nota_prediccion;
            $listado_estudiantes[$key]['observacion_prediccion'] = $datos_nota->observacion_prediccion;
        }

        $estudiantes = $listado_estudiantes;

        return compact('estudiantes');
    }

    public function fetchCursosByEstudiante($estudiante_id)
    {
        $cursos = Curso::join('grupos', 'grupos.id', '=', 'cursos.grupo_id')
        ->join('grupos_estudiantes', 'grupos_estudiantes.grupo_id', '=', 'grupos.id')
        ->select('cursos.*')
        ->where('grupos_estudiantes.estudiante_id', $estudiante_id)
        ->get();

        $listado_cursos = array();

        foreach ($cursos as $key => $curso) {

            $data_nota = Nota::where('curso_id', $curso->id)->where('estudiante_id', $estudiante_id)->first();

            $listado_cursos[$key]['curso_id'] = $curso->id;
            $listado_cursos[$key]['curso_nombre'] = $curso->asignatura->nombre;

            if(is_null($data_nota)) {
                $listado_cursos[$key]['nota_prediccion'] = '';
                $listado_cursos[$key]['observacion_prediccion'] = '';
            }
            else {
                $listado_cursos[$key]['nota_prediccion'] = is_null($data_nota->nota_prediccion)?'':$data_nota->nota_prediccion;
                $listado_cursos[$key]['observacion_prediccion'] = is_null($data_nota->observacion_prediccion)?'':$data_nota->observacion_prediccion;
            }
        }

        $cursos = $listado_cursos;

        return compact('cursos');
    }

    public function saveNota(NotaRequest $request) {
        try {
            DB::beginTransaction();
            
            $estudiante_id = $request->estudiante_id;
            $curso_id = $request->curso_id;
            $nota_value = $request->nota;
            $index = $request->index;

            $curso = Curso::find($request->curso_id);

            $periodos = Periodo::where('nombre',  'LIKE', '%' . $curso->grupo->anio . '%')->get();

            $nota = Nota::where('curso_id', $curso_id)->where('estudiante_id', $estudiante_id)->first();

            $array_notas = array();

            if (is_null($nota)) {
                foreach ($periodos as $key => $periodo) {
                    if ($key == $index) {
                        $array_notas[$key] = $nota_value;
                    }
                    else {
                        $array_notas[$key] = '';
                    }
                }

                Nota::create([
                    'curso_id' => $curso_id,
                    'estudiante_id' => $estudiante_id,
                    'notas' =>  implode(',', $array_notas),
                    'nota_final' => $nota_value
                ]);
            }
            else {
                $array_notas = explode(',', $nota->notas);
                foreach ($periodos as $key => $periodo) {
                    if ($key == $index) {
                        $array_notas[$key] = $nota_value;
                    }
                }
                $nota->notas = implode(',', $array_notas);

                $array_notas = array_map('intval', $array_notas);
                $promedio = array_sum($array_notas) / count($array_notas);
                $promedio = round($promedio, 2);

                $nota->nota_final = $promedio;
                $nota->save();
            }
            
            DB::commit();

            return [
                'success' => true,
                'message' => 'Nota registrada'
            ];
        } catch (Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function predecir(Request $request)
    {  
        try {
            DB::beginTransaction();

            $estudiante_id = $request->input('estudiante_id');
            $curso_id = $request->input('curso_id');

            $nota = Nota::where('estudiante_id', $estudiante_id)->where('curso_id', $curso_id)->first();
            
            $url_fastapi = env('URL_FASTAPI').'/test/';

            $request_body =  [
                "nota1" => $request->input('nota1'),
                "nota2" => $request->input('nota2'),
                "nota3" => $request->input('nota3'),
                "nota4" => $request->input('nota4'),
                "asistencia1" => $request->input('asistencia1'),
                "asistencia2" => $request->input('asistencia2'),
                "asistencia3" => $request->input('asistencia3'),
                "asistencia4" => $request->input('asistencia4'),
                "observacion1" => $request->input('observacion1'),
                "observacion2" => $request->input('observacion2'),
                "observacion3" => $request->input('observacion3'),
                "observacion4" => $request->input('observacion4'),
            ];

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json',
            ];
            $body = json_encode($request_body);

            $response = $client->request('GET', $url_fastapi, [
                'headers' => $headers,
                'body' => $body,
            ]);

            $response_array = json_decode($response->getBody());

            $nota_prediccion = 0;
            $observacion_prediccion = '';

            if(isset($response_array->NotaFinal)) {
                $nota_prediccion = number_format($response_array->NotaFinal, 2, '.', '');
                $observacion_prediccion = $response_array->ObservacionFinal;

                $nota->nota_prediccion = $nota_prediccion;
                $nota->observacion_prediccion = $observacion_prediccion;
                $nota->save();
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Paciente creado con exito',
                'nota_prediccion' => $nota_prediccion,
                'observacion_prediccion' => $observacion_prediccion,
            ];
        } catch (Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
