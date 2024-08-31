<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Empleado;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        return User::with('rol')->get();
    }

    public function fetchDataById($id)
    {
        $user = User::find($id);
        $persona = Persona::find($user->persona_id);

        if(is_null($persona)) {
            $data = [
                'id' => $user->id,
                'email' => $user->email,
                'rol_id' => $user->rol_id,
            ];
        }
        else {
            $data = [
                'id' => $user->id,
                'nombres' => $persona->nombres,
                'apellidos' => $persona->apellidos,
                'tipo_documento' => $persona->tipo_documento_id,
                'numero_documento' => $persona->numero_documento,
                'genero' => $persona->genero,
                'email' => $user->email,
                'rol_id' => $user->rol_id,
                'estado' => $user->state,
            ];
        }

        return json_encode($data);
    }

    public function store(UserCreateRequest $request)
    {
        try {
            $id = $request->input('id');

            $nombres = $request->input('nombres');
            $apellidos = $request->input('apellidos');

            DB::beginTransaction();
            
            
            $persona = Persona::create([
                'tipo_documento_id' => 1,
                'numero_documento' => $request->input('numero_documento'),
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'genero' => $request->input('genero'),
            ]);

            User::create([
                'name' => $nombres.' '.$apellidos,
                'email' => $request->email,
                'rol_id' => $request->rol_id,
                'persona_id' => $persona->id,
                'password' => Hash::make($request->password),
                'state' => $request->estado,
            ]);

            if($request->rol_id == config('contanst.ROL_COORDINADOR') || $request->rol_id == config('contanst.ROL_PROFESOR')) {
                Empleado::create([
                    'persona_id' => $persona->id,
                    'tipo' => $request->rol_id
                ]);
            }

            if($request->rol_id == config('contanst.ROL_ALUMNO')) {
                Estudiante::create([
                    'persona_id' => $persona->id
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => ($id) ? 'Usuario editado con exito' : 'Usuario creado con exito'
            ];
        } catch (Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function update(UserUpdateRequest $request)
    {
        try {
            $id = $request->input('id');
            $nombres = $request->input('nombres');
            $apellidos = $request->input('apellidos');

            DB::beginTransaction();
        
            $user = User::find($id);
            $user->name = $nombres.' '.$apellidos;
            $user->email = $request->email;
            if(!empty($request->password)) {
                $user->password = Hash::make($request->password);
            }
            $user->state = $request->estado;
            $user->save();

            $persona = Persona::find($user->persona_id);
            $persona->numero_documento = $request->numero_documento;
            $persona->nombres = $request->nombres;
            $persona->apellidos = $request->apellidos;
            $persona->genero = $request->genero;
            $persona->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Usuario editado con exito'
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
