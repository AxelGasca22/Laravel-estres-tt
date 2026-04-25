<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Psicologo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $psicologo = Psicologo::first();
        $users = User::where('role', 'paciente')->get();

        foreach ($users as $user) {
            Paciente::create([
                'user_id' => $user->id,
                'psicologo_id' => $psicologo->id,
                'edad' => rand(18, 30),
                'sexo' => rand(0, 1) ? 'M' : 'F',
                'nivel_estres_actual' => rand(3, 50),
                'semestre' => rand(1, 9),
                'numero_boleta' => str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
