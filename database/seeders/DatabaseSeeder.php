<?php

namespace Database\Seeders;

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
        // 1. Crear los roles
        $this->call([
            RoleSeeder::class,
        ]);

        // 2. Crear Usuario Administrador
        User::create([
            'name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'), // Contraseña fácil para desarrollo
            'role_id' => 1, // ID 1 = Admin
        ]);

        // 3. Crear un Juez de prueba (para que tengas con qué probar el login de juez)
        User::create([
            'name' => 'Juez',
            'last_name' => 'Uno',
            'email' => 'juez@admin.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2, // ID 2 = Juez
        ]);
    }
}
