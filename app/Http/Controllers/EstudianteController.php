<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstudianteController extends Controller
{
    public function index()
    {
        return Inertia::render('Estudiantes/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        return User::with('rol')->where('rol_id', config('contanst.ROL_ALUMNO'))->get();
    }
}
