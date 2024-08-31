<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsistenciaRequest;
use App\Http\Requests\CursoRequest;
use App\Models\Asistencia;
use App\Models\Clase;
use App\Models\Curso;
use App\Models\Empleado;
use App\Models\Estudiante;
use App\Models\Nota;
use App\Models\Periodo;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClaseController extends Controller
{
    public function index()
    {
        return Inertia::render('Clases/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        return Clase::all();
    }

    public function fetchLoadForm()
    {
        $usuario = Auth::user();
        
        $empleado = Empleado::where('persona_id', $usuario->persona_id)->first();

        $cursos = Curso::with('grupo')->where('empleado_id', $empleado->id)->get();
                
        $periodos = Periodo::where('nombre',  'LIKE', '%' . date('Y') . '%')->get();

        return compact('cursos', 'periodos');
    }

    public function fetchGrupoEstudiantes($clase_id, $curso_id)
    {
        $curso = Curso::find($curso_id);

        $estudiantes = Estudiante::join('grupos_estudiantes', 'estudiantes.id', '=', 'grupos_estudiantes.estudiante_id')
        ->select('estudiantes.*')
        ->where('grupos_estudiantes.grupo_id', $curso->grupo_id)
        ->get();

        $listado_estudiantes = array();

        foreach ($estudiantes as $key => $estudiante) {
            $listado_estudiantes[$key]['clase_id'] = $clase_id;
            $listado_estudiantes[$key]['estudiante_id'] = $estudiante->id;
            $listado_estudiantes[$key]['estudiante_nombre'] = $estudiante->persona->nombre_completo;
            
            $asistencia = Asistencia::where('clase_id', $clase_id)->where('estudiante_id', $estudiante->id)->first();
            
            if (is_null($asistencia)) {
                $listado_estudiantes[$key]['tipo'] = '';
                $listado_estudiantes[$key]['detalle'] = '';
                $listado_estudiantes[$key]['nota'] = '';
                $listado_estudiantes[$key]['observacion'] = 'N';
            }
            else {
                $listado_estudiantes[$key]['tipo'] = $asistencia->tipo;
                $listado_estudiantes[$key]['detalle'] = $asistencia->detalle;
                $listado_estudiantes[$key]['nota'] = $asistencia->nota;
                $listado_estudiantes[$key]['observacion'] = $asistencia->observacion;
            }
        }

        $estudiantes = $listado_estudiantes;

        return compact('estudiantes');
    }

    public function store(CursoRequest $request)
    {
        try {
            $id = $request->input('id');

            $clase = Clase::firstOrNew(['id' => $id]);
            $clase->curso_id = $request->input('curso_id');
            $clase->periodo_id = $request->input('periodo_id');
            $clase->fecha_clase = $request->input('fecha_clase');
            $clase->descripcion = $request->input('descripcion');
            $clase->estado = 1;
            $clase->save();

            return [
                'success' => true,
                'message' => ($id) ? 'Clase editada con exito' : 'Clase creada con exito'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function delete($id)
    {
        try {
            Clase::find($id)->delete();
            
            return [
                'success' => true,
                'message' => 'Eliminado con exito'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function storeAsistencia(AsistenciaRequest $request) {
        try {
            DB::beginTransaction();
            
            $estudiante_id = $request->estudiante_id;
            $clase_id = $request->clase_id;
            $tipo = $request->tipo;
            $nota_value = $request->nota;
            $observacion = $request->observacion;
            
            $clase = Clase::find($clase_id);
            $curso = Curso::find($clase->curso_id);

            $asistencia = Asistencia::where('clase_id', $clase_id)->where('estudiante_id', $estudiante_id)->first();

            if (is_null($asistencia)) {
                Asistencia::create([
                    'clase_id' => $clase_id,
                    'estudiante_id' => $estudiante_id,
                    'tipo' => $tipo,
                    'nota' => $nota_value,
                    'observacion' => $observacion,
                ]);
            }
            else {
                $asistencia->tipo = $tipo;
                $asistencia->nota = $nota_value;
                $asistencia->observacion = $observacion;
                $asistencia->save();
            }

            //save notas
            if (!is_null($nota_value)) {
                $nota = Nota::where('curso_id', $clase->curso_id)->where('estudiante_id', $estudiante_id)->first();
                $periodos = Periodo::where('nombre',  'LIKE', '%' . $curso->grupo->anio . '%')->get();
                
                if (is_null($nota)) {
                    foreach ($periodos as $key => $periodo) {
                        if ($periodo->id == $clase->periodo_id) {
                            $promedioNotas = Asistencia::where('estudiante_id', $estudiante_id)
                                ->where('periodo_id', $periodo->id)
                                ->join('clases', 'asistencias.clase_id', '=', 'clases.id')
                                ->avg('asistencias.nota');

                            $promedioNotas = number_format($promedioNotas, 2, ".", "");
                        
                            $array_notas[$key] = $promedioNotas;
                        }
                    }

                    Nota::create([
                        'curso_id' => $clase->curso_id,
                        'estudiante_id' => $estudiante_id,
                        'notas' =>  implode(',', $array_notas),
                        'nota_final' => $promedioNotas
                    ]);
                }
                else {
                    $array_notas = explode(',', $nota->notas);
                    foreach ($periodos as $key => $periodo) {
                        if ($periodo->id == $clase->periodo_id) {
                            $promedioNotas = Asistencia::where('estudiante_id', $estudiante_id)
                                ->where('periodo_id', $periodo->id)
                                ->join('clases', 'asistencias.clase_id', '=', 'clases.id')
                                ->avg('asistencias.nota');

                            $promedioNotas = number_format($promedioNotas, 2, ".", "");

                            $array_notas[$key] = $promedioNotas;
                        }
                    }

                    $nota->notas = implode(',', $array_notas);

                    $array_notas = array_map('intval', $array_notas);
                    $promedio = array_sum($array_notas) / count($array_notas);
                    $promedio = round($promedio, 2);

                    $nota->nota_final = $promedio;
                    $nota->save();
                }
            }
            
            DB::commit();

            return [
                'success' => true,
                'message' => 'Asistencia registrada'
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
