<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            PsicologoSeeder::class,
            PacienteSeeder::class,
            TestSeeder::class,
            PreguntaSeeder::class,
            RespuestaPreguntaSeeder::class,
            CategoriaSeeder::class,
            ActividadSeeder::class,
            RecursoActividadSeeder::class,
            SesionSeeder::class,
            ProgresoActividadSeeder::class,
            CalificacionSeeder::class,
            HistorialCalificacionSeeder::class,
        ]);
    }
}
