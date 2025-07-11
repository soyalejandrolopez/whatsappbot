<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@chatbot.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'phone' => '+52 55 1234 5678',
            'bio' => 'Administrador principal del sistema de chatbot',
            'preferences' => json_encode([
                'language' => 'es',
                'timezone' => 'America/Mexico_City',
                'notifications' => true
            ])
        ]);

        // Crear usuario agente
        User::create([
            'name' => 'Agente de Soporte',
            'email' => 'agente@chatbot.com',
            'password' => Hash::make('agente123'),
            'role' => 'agent',
            'is_active' => true,
            'phone' => '+52 55 8765 4321',
            'bio' => 'Agente de atención al cliente',
            'preferences' => json_encode([
                'language' => 'es',
                'timezone' => 'America/Mexico_City',
                'notifications' => true
            ])
        ]);

        // Crear usuario demo
        User::create([
            'name' => 'Usuario Demo',
            'email' => 'demo@chatbot.com',
            'password' => Hash::make('demo123'),
            'role' => 'user',
            'is_active' => true,
            'phone' => '+52 55 9999 0000',
            'bio' => 'Usuario de demostración',
            'preferences' => json_encode([
                'language' => 'es',
                'timezone' => 'America/Mexico_City',
                'notifications' => false
            ])
        ]);
    }
}
