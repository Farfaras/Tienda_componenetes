<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@admin.com');
        $password = env('ADMIN_PASSWORD', 'admin123456');

        Usuario::updateOrCreate(
            ['email' => $email],
            [
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'password' => Hash::make($password),
                'direccion' => 'Oficina Central',
                'estado' => true,
                'id_rol' => 1,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}
