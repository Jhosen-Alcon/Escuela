<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfesorController extends Controller
{
    public function index()
    {
        return Inertia::render('Profesores/Index', [
        ]);
    }

    public function fetchDataTable()
    {
        return User::with('rol')->where('rol_id', config('contanst.ROL_PROFESOR'))->get();
    }
}
