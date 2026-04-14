<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@mediahub.com'],
            [
                'name' => 'Admin MediaHub',
                'nombre_usuario' => 'admin',
                'fecha_nacimiento' => '1990-01-01',
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'gestor@mediahub.com'],
            [
                'name' => 'Gestor MediaHub',
                'nombre_usuario' => 'gestor',
                'fecha_nacimiento' => '1995-01-01',
                'password' => Hash::make('123456'),
                'role' => 'gestor',
            ]
        );
    }
}
