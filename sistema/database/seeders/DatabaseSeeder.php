<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear primero el rol de médico
        Role::firstOrCreate(
            ['name' => 'medico'],
            ['description' => 'medico']
        );

        // Crear o recuperar el rol administrador
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrador'],
            ['description' => 'revisa todo']
        );

        // Registrar o actualizar la cuenta de Kassandra
        User::updateOrCreate(
            ['email' => 'kassandra@sanjuan.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Kassandra',
                'dni' => '00000001',
                'phone' => null,
                'tuition' => null,
                'password' => 'KassSJB#2026!',
            ]
        );
    }
}
