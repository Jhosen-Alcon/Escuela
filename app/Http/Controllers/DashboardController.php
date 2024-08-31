<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
        ]);
    }

    public function fetch()
    {
        if (Auth::user()->rol_id == 4) {
            $usuario = User::find(auth()->id());
            $estudiante = Estudiante::where('persona_id', $usuario->persona_id)->first();

            $cantidad_cursos = Curso::join('grupos', 'grupos.id', '=', 'cursos.grupo_id')
                ->join('grupos_estudiantes', 'grupos_estudiantes.grupo_id', '=', 'grupos.id')
                ->select('cursos.*')
                ->where('grupos_estudiantes.estudiante_id', $estudiante->id)
                ->count();

            $total_asistencia = Asistencia::where('estudiante_id', $estudiante->id)->where('tipo', 'A')->count();
            $total_faltas = Asistencia::where('estudiante_id', $estudiante->id)->where('tipo', 'F')->count();

            return compact('cantidad_cursos', 'total_asistencia', 'total_faltas');   
        }
        else {
            $cantidad_cursos = Curso::join('grupos', 'grupos.id', '=', 'cursos.grupo_id')
                ->join('grupos_estudiantes', 'grupos_estudiantes.grupo_id', '=', 'grupos.id')
                ->select('cursos.*')
                ->count();

            $total_asistencia = Asistencia::where('tipo', 'A')->count();
            $total_faltas = Asistencia::where('tipo', 'F')->count();

            $asistencias = Asistencia::join('clases', 'clases.id', '=', 'asistencias.clase_id')
                            ->select('tipo', 'fecha_clase', 'clase_id')
                            ->limit(10)
                            ->get();

            $array_labels = array();
            $array_dataset = array();

            $array_dataset[0]['label'] = 'Asistencia';
            $array_dataset[0]['backgroundColor'] = '#46c35f';
            $array_dataset[0]['borderColor'] = '#46c35f';
            $array_dataset[1]['label'] = 'Falta';
            $array_dataset[1]['backgroundColor'] = '#E91E63';
            $array_dataset[1]['borderColor'] = '#E91E63';
            $array_dataset[2]['label'] = 'Justificado';
            $array_dataset[2]['backgroundColor'] = '#57c7d4';
            $array_dataset[2]['borderColor'] = '#57c7d4';

            foreach($asistencias as $key => $asistencia) {
                array_push($array_labels, $asistencia->fecha_clase);

                $cantidad_asistencia = Asistencia::where('clase_id', $asistencia->clase_id)->where('tipo', 'A')->count();
                $cantidad_falta = Asistencia::where('clase_id', $asistencia->clase_id)->where('tipo', 'F')->count();
                $cantidad_justificado = Asistencia::where('clase_id', $asistencia->clase_id)->where('tipo', 'J')->count();

                $array_dataset[0]['data'][$key] = $cantidad_asistencia;
                $array_dataset[1]['data'][$key] = $cantidad_falta;
                $array_dataset[2]['data'][$key] = $cantidad_justificado;
            }

            return compact('cantidad_cursos', 'total_asistencia', 'total_faltas', 'array_labels', 'array_dataset');   
        }
          
    }

    public function fetchDataTable()
    {
        return Asignatura::all();
    }
}
