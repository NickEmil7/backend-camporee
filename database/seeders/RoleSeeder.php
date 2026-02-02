<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rol 1: Admin
        Role::create([
            'id' => 1,
            'name' => 'Admin',
            // 'description' => '...' // ELIMINADO
        ]);

        // Rol 2: Juez
        Role::create([
            'id' => 2,
            'name' => 'Juez',
            // 'description' => '...' // ELIMINADO
        ]);
    }
}
