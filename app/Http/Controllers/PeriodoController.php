<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodoRequest;
use App\Http\Resources\PeriodoCollection;
use App\Models\Periodo;
use Exception;
use Inertia\Inertia;

class PeriodoController extends Controller
{
    public function index()
    {
        return Inertia::render('Periodos/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        $periodos = Periodo::all();
        return new PeriodoCollection($periodos);
    }

    public function store(PeriodoRequest $request)
    {
        try {
            $id = $request->input('id');

            $anio = $request->input('anio');
            $semestre = $request->input('semestre');

            $periodo = Periodo::firstOrNew(['id' => $id]);
            $periodo->nombre = $anio.'-'.$semestre;
            $periodo->save();

            return [
                'success' => true,
                'message' => ($id) ? 'Usuario editado con exito' : 'Usuario creado con exito'
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
            Periodo::find($id)->delete();
            
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
