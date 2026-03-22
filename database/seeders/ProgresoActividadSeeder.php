<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Paciente;
use App\Models\ProgresoActividad;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgresoActividadSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = Paciente::all();
        $actividades = Actividad::where('modulo', 1)->get();

        if ($actividades->isEmpty()) {
            return;
        }

        foreach ($pacientes as $paciente) {
            foreach ($actividades as $actividad) {
                ProgresoActividad::updateOrCreate(
                    [
                        'paciente_id' => $paciente->id,
                        'actividad_id' => $actividad->id,
                    ],
                    [
                        'fecha' => Carbon::now()->toDateString(),
                        'progreso_porcentaje' => 0,
                        'comentarios' => 'Actividad asignada por defecto al módulo 1',
                        'estado' => 'pendiente',
                    ]
                );
            }
        }
    }
}
