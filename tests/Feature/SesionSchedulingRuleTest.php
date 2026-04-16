<?php

use App\Models\Paciente;
use App\Models\Psicologo;
use App\Models\Sesion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('blocks a patient from booking another appointment within seven days', function () {
    $psychologistUser = User::factory()->create([
        'role' => 'psicologo',
    ]);

    $psychologist = Psicologo::create([
        'user_id' => $psychologistUser->id,
        'cedula_profesional' => 'ABC123456',
    ]);

    $patientUser = User::factory()->create([
        'role' => 'paciente',
    ]);

    $patient = Paciente::create([
        'user_id' => $patientUser->id,
        'psicologo_id' => $psychologist->id,
        'edad' => 21,
        'sexo' => 'F',
        'nivel_estres_actual' => 'moderado',
        'semestre' => '6',
    ]);

    Sesion::create([
        'psicologo_id' => $psychologist->id,
        'paciente_id' => $patient->id,
        'fecha' => '2026-04-16',
        'hora' => '11:00:00',
        'tipo_sesion' => null,
        'observaciones' => 'Primera sesion',
    ]);

    Sanctum::actingAs($patientUser);

    $response = postJson('/api/sesiones', [
        'psicologo_id' => $psychologist->id,
        'fecha' => '2026-04-22',
        'hora' => '09:00',
        'observaciones' => 'Seguimiento',
    ]);

    $response
        ->assertStatus(422)
        ->assertJson([
            'message' => 'Solo puedes agendar una nueva cita despues de 7 dias de tu ultima cita agendada',
            'next_available_date' => '2026-04-23',
        ]);
});
