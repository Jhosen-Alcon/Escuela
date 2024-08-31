<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEstudiante extends Model
{
    protected $table = 'grupos_estudiantes';

    protected $fillable = [
        'grupo_id',
        'estudiante_id'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
