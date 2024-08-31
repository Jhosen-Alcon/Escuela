<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'clase_id',
        'estudiante_id',
        'tipo',
        'detale',
        'nota',
        'observacion',
    ];
}
