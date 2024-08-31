<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $appends = ['nombre_completo'];

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'nombres',
        'apellidos',
        'genero',
    ];

    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
