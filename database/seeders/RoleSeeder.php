<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //DB::table('roles')->delete(1);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Juez']);
        Role::create(['name' => 'Director']);
    }
}
