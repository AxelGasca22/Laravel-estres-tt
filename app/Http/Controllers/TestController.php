<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Calificacion;
use App\Models\Paciente;
use App\Models\HistorialCalificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TestController extends Controller
{
    /**
     * Devuelve las preguntas de un test con sus posibles respuestas.
     */
    public function preguntas(Test $test)
    {
        $test->load([
            'preguntas' => function ($q) {
                $q->orderBy('id');
            },
            'preguntas.respuestas' => function ($q) {
                // Ordenar por valor para mantener Nunca..Muy a menudo
                $q->orderBy('valor');
            },
        ]);

        $data = [
            'test_id' => $test->id,
            'preguntas' => $test->preguntas->map(function ($p) {
                return [
                    'id' => $p->id,
                    'texto' => $p->texto_pregunta,
                    'tipo' => $p->tipo,
                    'respuestas' => $p->respuestas->map(function ($r) {
                        return [
                            'id' => $r->id,
                            'texto' => $r->texto_respuesta,
                            'valor' => (int) $r->valor,
                        ];
                    })->values(),
                ];
            })->values(),
        ];

        return response()->json($data);
    }

    /**
     * Guarda el resultado del test (nivel de estrés actual) para el paciente autenticado.
     */
    public function resultado(Request $request, Test $test)
    {
        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0'],
        ]);

        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();
        if (!$paciente) {
            return response()->json([
                'message' => 'Paciente no encontrado para el usuario autenticado',
            ], 404);
        }

        $score = (int) $validated['score'];
        $fecha = Carbon::now();
        $categoria = $this->categoriaPorValor($score);

        $calificacion = Calificacion::create([
            'paciente_id' => $paciente->id,
            'test_id' => $test->id,
            'fecha_realizacion' => $fecha->toDateString(),
            'calificacion_general' => $score,
            'categoria' => $categoria,
        ]);

        HistorialCalificacion::create([
            'calificacion_id' => $calificacion->id,
            'fecha' => $fecha->toDateString(),
            'valor' => $score,
            'categoria' => $categoria,
        ]);

        $paciente->nivel_estres_actual = $score;
        $paciente->save();

        return (new \App\Http\Resources\PacienteResource($paciente))
            ->additional([
                'message' => 'Nivel de estrés actualizado',
            ]);
    }

    private function categoriaPorValor(int $valor): string
    {
        if ($valor <= 19) {
            return 'Bajo';
        }

        if ($valor <= 25) {
            return 'Moderado';
        }

        return 'Alto';
    }
}
