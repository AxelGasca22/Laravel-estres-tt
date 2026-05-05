<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Psicologo;
use App\Models\RespuestaJournaling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RespuestaJournalingController extends Controller
{
    // ─── Endpoints para Paciente ───────────────────────────────────────────────

    /**
     * POST /api/respuestas-journaling
     * El paciente autenticado guarda una respuesta de journaling.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (! $paciente) {
            return response()->json(['message' => 'Perfil de paciente no encontrado.'], 404);
        }

        $validated = $request->validate([
            'actividad_id'      => 'required|integer|exists:actividades,id',
            'titulo'            => 'nullable|string|max:255',
            'respuesta'         => 'required|string',
            'nivel_estres'      => 'nullable|string|max:100',
            'estado_animo'      => 'nullable|string|max:100',
            'visible_psicologo' => 'boolean',
        ]);

        $respuesta = RespuestaJournaling::create([
            'paciente_id'       => $paciente->id,
            'actividad_id'      => $validated['actividad_id'],
            'titulo'            => $validated['titulo'] ?? null,
            'respuesta'         => $validated['respuesta'],
            'nivel_estres'      => $validated['nivel_estres'] ?? null,
            'estado_animo'      => $validated['estado_animo'] ?? null,
            'visible_psicologo' => $validated['visible_psicologo'] ?? true,
        ]);

        return response()->json($respuesta->load('actividad'), 201);
    }

    /**
     * GET /api/respuestas-journaling
     * El paciente autenticado lista todas sus respuestas.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (! $paciente) {
            return response()->json(['message' => 'Perfil de paciente no encontrado.'], 404);
        }

        $respuestas = RespuestaJournaling::with('actividad')
            ->where('paciente_id', $paciente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($respuestas);
    }

    /**
     * GET /api/respuestas-journaling/{id}
     * El paciente autenticado ve una respuesta específica suya.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (! $paciente) {
            return response()->json(['message' => 'Perfil de paciente no encontrado.'], 404);
        }

        $respuesta = RespuestaJournaling::with('actividad')
            ->where('id', $id)
            ->where('paciente_id', $paciente->id)
            ->first();

        if (! $respuesta) {
            return response()->json(['message' => 'Respuesta no encontrada.'], 404);
        }

        return response()->json($respuesta);
    }

    /**
     * DELETE /api/respuestas-journaling/{id}
     * El paciente autenticado elimina una respuesta suya.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (! $paciente) {
            return response()->json(['message' => 'Perfil de paciente no encontrado.'], 404);
        }

        $respuesta = RespuestaJournaling::where('id', $id)
            ->where('paciente_id', $paciente->id)
            ->first();

        if (! $respuesta) {
            return response()->json(['message' => 'Respuesta no encontrada.'], 404);
        }

        $respuesta->delete();

        return response()->json(['message' => 'Respuesta eliminada correctamente.']);
    }

    // ─── Endpoints para Psicólogo ──────────────────────────────────────────────

    /**
     * GET /api/psicologo/pacientes/{paciente}/journaling
     * El psicólogo ve todas las respuestas de journaling de uno de sus pacientes.
     */
    public function pacienteJournaling(Request $request, int $pacienteId): JsonResponse
    {
        $user = $request->user();
        $psicologo = Psicologo::where('user_id', $user->id)->first();

        if (! $psicologo) {
            return response()->json(['message' => 'Perfil de psicólogo no encontrado.'], 403);
        }

        $paciente = Paciente::where('id', $pacienteId)
            ->where('psicologo_id', $psicologo->id)
            ->first();

        if (! $paciente) {
            return response()->json(['message' => 'Paciente no encontrado o no pertenece a este psicólogo.'], 404);
        }

        $respuestas = RespuestaJournaling::with('actividad')
            ->where('paciente_id', $paciente->id)
            ->where('visible_psicologo', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($respuestas);
    }

    /**
     * GET /api/psicologo/respuestas-journaling/{id}
     * El psicólogo ve el detalle de una respuesta de journaling de uno de sus pacientes.
     */
    public function showPsicologo(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $psicologo = Psicologo::where('user_id', $user->id)->first();

        if (! $psicologo) {
            return response()->json(['message' => 'Perfil de psicólogo no encontrado.'], 403);
        }

        $respuesta = RespuestaJournaling::with(['actividad', 'paciente.user'])
            ->where('id', $id)
            ->where('visible_psicologo', true)
            ->first();

        if (! $respuesta) {
            return response()->json(['message' => 'Respuesta no encontrada.'], 404);
        }

        // Verify the patient belongs to this psychologist
        if ($respuesta->paciente->psicologo_id !== $psicologo->id) {
            return response()->json(['message' => 'Acceso no autorizado.'], 403);
        }

        return response()->json($respuesta);
    }
}
