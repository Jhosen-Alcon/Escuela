<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $table = 'clases';

    protected $with = ['curso', 'periodo'];

    protected $fillable = [
        'curso_id',
        'description',
        'fecha_clase',
        'periodo_id',
        'estado'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }
}
