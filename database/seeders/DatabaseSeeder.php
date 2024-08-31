<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Persona::create([
            'id' => 1,
            'tipo_documento_id' => 1,
            'numero_documento' => '11111111',
            'nombres' => 'Administrador',
            'apellidos' => ''
        ]);

        User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'rol_id' => 1,
            'persona_id' => 1,
            'password' => Hash::make('12345678'),
            'state' => 1
        ]);
    }
}
