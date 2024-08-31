<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'notas';

    protected $fillable = [
        'curso_id',
        'estudiante_id',
        'notas',
        'nota_final',
        'nota_prediccion',
        'observacion_prediccion',
    ];
}
