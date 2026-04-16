<?php

namespace App\Http\Controllers;

use App\Http\Resources\SesionResource;
use App\Models\Sesion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user?->role === 'paciente' && $user->paciente) {
            $sesiones = Sesion::withTrashed()
                ->with(['psicologo.user'])
                ->where('paciente_id', $user->paciente->id)
                ->orderByDesc('fecha')
                ->orderByDesc('hora')
                ->get();

            return SesionResource::collection($sesiones);
        }

        $psicologo = $user?->psicologo;
        if (!$psicologo) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $week = $request->query('week', now()->toDateString());
        $start = Carbon::parse($week)->startOfWeek(Carbon::MONDAY);
        $end   = Carbon::parse($week)->endOfWeek(Carbon::FRIDAY);

        $sesiones = Sesion::with(['paciente.user'])
            ->where('psicologo_id', $psicologo->id)
            ->whereBetween('fecha', [$start->toDateString(), $end->toDateString()])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        return SesionResource::collection($sesiones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'psicologo_id' => ['required', 'exists:psicologos,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'regex:/^\d{2}:\d{2}$/'], // HH:mm
            'observaciones' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $paciente = $user?->paciente;

        if (!$paciente) {
            return response()->json([
                'message' => 'Solo pacientes pueden agendar citas'
            ], 403);
        }

        if (!$paciente->psicologo_id) {
            return response()->json([
                'message' => 'No tienes un psicologo asignado'
            ], 422);
        }

        if ((int) $paciente->psicologo_id !== (int) $request->psicologo_id) {
            return response()->json([
                'message' => 'Solo puedes agendar citas con tu psicologo asignado'
            ], 422);
        }

        $horaNormalizada = Carbon::createFromFormat('H:i', $request->hora)
            ->format('H:i:s');

        $fechaSolicitada = Carbon::parse($request->fecha)->startOfDay();
        $ultimaSesion = Sesion::query()
            ->where('paciente_id', $paciente->id)
            ->whereNull('deleted_at')
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->first();

        if ($ultimaSesion) {
            $proximaFechaDisponible = Carbon::parse($ultimaSesion->fecha)
                ->startOfDay()
                ->addDays(7);

            if ($fechaSolicitada->lt($proximaFechaDisponible)) {
                return response()->json([
                    'message' => 'Solo puedes agendar una nueva cita despues de 7 dias de tu ultima cita agendada',
                    'next_available_date' => $proximaFechaDisponible->toDateString(),
                ], 422);
            }
        }

        $exists = Sesion::where('psicologo_id', $request->psicologo_id)
            ->where('fecha', $request->fecha)
            ->where('hora', $horaNormalizada)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ese horario ya esta ocupado'
            ], 422);
        }

        $sesion = Sesion::create([
            'psicologo_id' => $request->psicologo_id,
            'paciente_id' => $paciente->id,
            'fecha' => $request->fecha,
            'hora' => $horaNormalizada,
            'tipo_sesion' => null,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json([
            'message' => 'Cita agendada correctamente',
            'sesion' => $sesion,
        ], 201);
    }

    /**
     * Return booked times for a psychologist on a date.
     */
    public function bookedTimes(Request $request)
    {
        $request->validate([
            'psicologo_id' => ['required', 'exists:psicologos,id'],
            'fecha' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $paciente = $user?->paciente;
        if ($paciente && (int) $paciente->psicologo_id !== (int) $request->psicologo_id) {
            return response()->json([
                'message' => 'Solo puedes consultar horarios de tu psicologo asignado'
            ], 403);
        }

        $times = Sesion::where('psicologo_id', $request->psicologo_id)
            ->where('fecha', $request->fecha)
            ->orderBy('hora')
            ->pluck('hora')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->values();

        return response()->json([
            'data' => $times,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sesion $sesion)
    {
        $user = Auth::user();
        if ($user?->role !== 'psicologo' || !$user->psicologo || $sesion->psicologo_id !== $user->psicologo->id) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $request->validate([
            'hora' => ['required', 'regex:/^\d{2}:\d{2}$/'], // HH:mm
            'fecha' => ['sometimes', 'date'],
            'modalidad' => ['in:virtual,presencial'],
            'notas' => ['nullable', 'string'],
        ]);

        // Normalizar hora a HH:mm:ss
        $horaNormalizada = Carbon::createFromFormat('H:i', $request->hora)
            ->format('H:i:s');

        $fechaObjetivo = $request->fecha ?? $sesion->fecha;
        $exists = Sesion::where('psicologo_id', $sesion->psicologo_id)
            ->where('fecha', $fechaObjetivo)
            ->where('hora', $horaNormalizada)
            ->where('id', '!=', $sesion->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ese horario ya esta ocupado'
            ], 422);
        }

        $sesion->update([
            'hora' => $horaNormalizada,
            'fecha' => $fechaObjetivo,
            'tipo_sesion' => $request->modalidad,
            'observaciones' => $request->notas ?? $sesion->observaciones,
        ]);

        return response()->json([
            'message' => 'Cita actualizada correctamente',
            'sesion' => new SesionResource($sesion->fresh(['paciente.user', 'psicologo.user'])),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sesion $sesion)
    {
        $user = Auth::user();
        if ($user?->role !== 'psicologo' || !$user->psicologo || $sesion->psicologo_id !== $user->psicologo->id) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $sesion->delete();

        return response()->json([
            'message' => 'Sesión eliminada correctamente'
        ], 200);
    }
}
