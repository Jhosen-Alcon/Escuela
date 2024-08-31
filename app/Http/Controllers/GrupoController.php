<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrupoRequest;
use App\Models\Asignatura;
use App\Models\Curso;
use App\Models\Empleado;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\GrupoEstudiante;
use App\Models\Nivel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GrupoController extends Controller
{
    public function index()
    {
        return Inertia::render('Grupos/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        return Grupo::with('empleado')->get();
    }

    public function fetchDataById($id)
    {
        $grupo = Grupo::find($id);

        $nombre = $grupo->nombre;
        $nombre = explode('-', $nombre);

        $data = [
            'id' => $grupo->id,
            'empleado_id' => $grupo->empleado_id,
            'grado' => $nombre[0],
            'seccion' => $nombre[1],
            'nivel_id' => $grupo->nivel_id,
            'anio' => $grupo->anio,
        ];

        return json_encode($data);
    }

    public function fetchGrupoEstudiantes($id)
    {
        $grupo_estudiantes = GrupoEstudiante::with('estudiante')->where('grupo_id', $id)->get();
        $estudiantes = Estudiante::all();

        return compact('estudiantes', 'grupo_estudiantes');
    }

    public function fetchGrupoAsignaturas($id)
    {
        $grupo_asignaturas = Curso::where('grupo_id', $id)->get();
        $asignaturas = Asignatura::all();

        return compact('asignaturas', 'grupo_asignaturas');
    }

    public function fechAllTables()
    {
        $niveles = Nivel::all();
        $empleados = Empleado::with('persona')->where('tipo', config('contanst.ROL_PROFESOR'))->get();

        return compact('niveles', 'empleados');
    }

    public function store(GrupoRequest $request)
    {
        try {
            $id = $request->input('id');

            $grupo = Grupo::firstOrNew(['id' => $id]);
            $grupo->empleado_id = $request->input('empleado_id');
            $grupo->nivel_id = $request->input('nivel_id');
            $grupo->nombre = $request->input('grado').'-'.$request->input('seccion');
            $grupo->anio = $request->input('anio');
            $grupo->estado = 1;
            $grupo->save();

            return [
                'success' => true,
                'message' => ($id) ? 'Grupo editado con exito' : 'Grupo creado con exito'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function store_estudiantes(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'grupo_id' => [
                    'required',
                    'exists:grupos,id',
                    Rule::unique('grupos_estudiantes')->where(function ($query) use ($request) {
                        return $query->where('estudiante_id', $request->input('estudiante_id'));
                    }),
                ],
                'estudiante_id' => [
                    'required',
                    'exists:estudiantes,id',
                    Rule::unique('grupos_estudiantes')->where(function ($query) use ($request) {
                        return $query->where('grupo_id', $request->input('grupo_id'));
                    }),
                ],
            ]);

            GrupoEstudiante::create([
                'grupo_id' => $validatedData['grupo_id'],
                'estudiante_id' => $validatedData['estudiante_id'],
            ]);

            return [
                'success' => true,
                'message' => 'Estudiante agregado con éxito'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function store_asignaturas(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'grupo_id' => [
                    'required',
                    'exists:grupos,id',
                    Rule::unique('cursos')->where(function ($query) use ($request) {
                        return $query->where('asignatura_id', $request->input('asignatura_id'));
                    }),
                ],
                'asignatura_id' => [
                    'required',
                    'exists:asignaturas,id',
                    Rule::unique('cursos')->where(function ($query) use ($request) {
                        return $query->where('grupo_id', $request->input('grupo_id'));
                    }),
                ],
                'empleado_id' => [
                    'required',
                ],
            ]);

            Curso::create([
                'grupo_id' => $validatedData['grupo_id'],
                'asignatura_id' => $validatedData['asignatura_id'],
                'empleado_id' => $validatedData['empleado_id'],
            ]);

            return [
                'success' => true,
                'message' => 'Curso agregado con éxito'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function delete_estudiantes($id)
    {
        try {
            GrupoEstudiante::find($id)->delete();
            
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

    public function delete_asignaturas($id)
    {
        try {
            Curso::find($id)->delete();
            
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
}
