<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Rol::updateOrCreate(
            ['id_rol' => 1],
            ['nombre' => 'Administrador']
        );

        Rol::updateOrCreate(
            ['id_rol' => 2],
            ['nombre' => 'Vendedor']
        );
    }
}
