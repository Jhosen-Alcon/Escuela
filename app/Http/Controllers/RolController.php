<?php

namespace App\Http\Controllers;

use App\Models\Rol;

class RolController extends Controller
{
    public function fetchAll()
    {
        return Rol::where('id','!=', 4)->get();
    }
}
