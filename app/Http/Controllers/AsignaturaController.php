<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AsignaturaController extends Controller
{
    public function index()
    {
        return Inertia::render('Asignaturas/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        return Asignatura::all();
    }
}
