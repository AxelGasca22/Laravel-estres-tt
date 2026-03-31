<?php

namespace App\Http\Controllers;

use App\Http\Resources\PacienteCollection;
use App\Models\Calificacion;
use App\Models\Paciente;
use App\Models\ProgresoActividad;
use App\Models\Sesion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->input('role') === 'psicologo') {
            $user = auth()->user();
            $psicologo = $user->psicologo;
            $totalSesionesProximas = Sesion::whereHas('paciente', function ($q) use ($psicologo) {
                $q->where('psicologo_id', $psicologo->id);
            })
                ->where('fecha', '>', now())
                ->count();

            $pacientes = $psicologo->pacientes()
                ->with([
                    'user',
                    'ultimaSesion',
                    'progresoActividad',
                ])
                ->get();

            $pacientesSinAsignar = Paciente::whereNull('psicologo_id')
                ->with('user')
                ->get();

            return (new PacienteCollection($pacientes))
                ->additional([
                    'sesiones_proximas' => $totalSesionesProximas,
                    'pacientes_sin_asignar' => $pacientesSinAsignar
                ]);
        }
    }

    public function datosPaciente(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (!$paciente) {
            return response()->json([
                'message' => 'Paciente no encontrado para el usuario autenticado',
            ], 404);
        }

        return response()->json([
            'id' => $paciente->id,
            'nombre' => $paciente->user->name,
            'semestre' => $paciente->semestre,
            'sexo' => $paciente->sexo,
            'edad' => $paciente->edad,
        ]);
    }

    public function actualizarDatosPaciente(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (!$paciente) {
            return response()->json([
                'message' => 'Paciente no encontrado para el usuario autenticado',
            ], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'semestre' => 'sometimes|integer|min:1|max:8',
            'sexo' => 'sometimes|string|in:M,F,Otro',
            'edad' => 'sometimes|integer|min:0|max:120',
        ]);

        if (isset($validatedData['nombre'])) {
            $paciente->user->name = $validatedData['nombre'];
            $paciente->user->save();
        }
        if (isset($validatedData['semestre'])) {
            $paciente->semestre = $validatedData['semestre'];
        }
        if (isset($validatedData['sexo'])) {
            $paciente->sexo = $validatedData['sexo'];
        }
        if (isset($validatedData['edad'])) {
            $paciente->edad = $validatedData['edad'];
        }

        $paciente->save();

        return response()->json([
            'message' => 'Datos del paciente actualizados correctamente',
            'data' => [
                'id' => $paciente->id,
                'nombre' => $paciente->user->name,
                'semestre' => $paciente->semestre,
                'sexo' => $paciente->sexo,
                'edad' => $paciente->edad,
            ],
        ]);
    }

    /**
     * Devuelve el historial de niveles de estrés del paciente autenticado.
     */
    public function estresRegistros(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();
        if (!$paciente) {
            return response()->json([
                'message' => 'Paciente no encontrado para el usuario autenticado',
            ], 404);
        }

        $puntos = DB::table('historial_calificaciones as hc')
            ->join('calificaciones as c', 'c.id', '=', 'hc.calificacion_id')
            ->where('c.paciente_id', $paciente->id)
            ->orderBy('hc.fecha', 'asc')
            ->select('hc.valor', 'hc.fecha')
            ->get()
            ->map(function ($item) {
                return [
                    'score' => (int) round($item->valor),
                    'created_at' => Carbon::parse($item->fecha)->startOfDay()->toIso8601String(),
                    'source' => 'history',
                ];
            })
            ->values();

        // Backward-compatibility for legacy rows only stored in calificaciones.
        if ($puntos->isEmpty()) {
            $puntos = $paciente->calificaciones()
                ->orderBy('fecha_realizacion', 'asc')
                ->get()
                ->map(function (Calificacion $calificacion) {
                    return [
                        'score' => (int) round($calificacion->calificacion_general),
                        'created_at' => Carbon::parse($calificacion->fecha_realizacion)->startOfDay()->toIso8601String(),
                        'source' => 'legacy',
                    ];
                })
                ->values();
        }

        $nivelActual = $paciente->nivel_estres_actual;
        if ($nivelActual !== null && $nivelActual > 0) {
            $ultimoScore = $puntos->isNotEmpty() ? (int) ($puntos->last()['score'] ?? -1) : -1;

            $puntos->push([
                'score' => (int) round($nivelActual),
                'created_at' => now()->toIso8601String(),
                'source' => 'current',
            ]);

            // Avoid plotting the same score twice when current state already exists in history.
            if ($ultimoScore === (int) round($nivelActual)) {
                $puntos->pop();
            }
        }

        return response()->json([
            'data' => $puntos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Confirm the user's email address.
     */
    public function confirmarCuenta(Request $request, User $user)
    {
        if (! $request->hasValidSignature()) {
            return response('El enlace de confirmación es inválido o ha expirado.', 401);
        }

        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        return redirect()->away($frontendUrl . '/cuenta-confirmada-paciente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {

        Carbon::setLocale('es');

        // if($request->input('role') === 'psicologo') {
        // 1. Obtener información básica del paciente y su usuario
        $paciente = Paciente::with('user')->findOrFail($id);

        // 2. Datos para el Gráfico
        $registros = DB::table('calificaciones')
            ->where('paciente_id', $id)
            ->orderBy('fecha_realizacion', 'desc')
            ->take(7)
            ->get();

        // Luego los reordenamos cronológicamente para el gráfico (izq a der)
        $historialOrdenado = $registros->sortBy('fecha_realizacion');

        // Preparamos los arrays simples (usando values() para evitar índices extraños)
        $labels = $historialOrdenado->map(function ($item) {
            // Formateamos la fecha: "10 Feb"
            return \Carbon\Carbon::parse($item->fecha_realizacion)->format('d M');
        })->values()->all();

        $valores = $historialOrdenado->pluck('calificacion_general')->values()->all();

        // 3. Estadísticas de Sesiones
        $totalSesiones = Sesion::where('paciente_id', $id)->count();

        $proximaSesion = Sesion::where('paciente_id', $id)
            ->where('fecha', '>=', now())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->first();

        // 4. Módulos y Actividades (Progreso y Tabla Reciente)
        // Hacemos join con 'actividades' para obtener el nombre y descripción
        $actividades = ProgresoActividad::where('paciente_id', $id)
            ->with('actividad')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculamos estadísticas de tareas
        $totalActividades = $actividades->count();
        $actividadesCompletadas = $actividades->where('estado', 'completado')->count();
        $porcentajeGlobal = $totalActividades > 0
            ? round(($actividadesCompletadas / $totalActividades) * 100)
            : 0;

        // 5. Estructurar respuesta JSON para el Frontend
        return response()->json([
            'perfil' => [
                'id' => $paciente->id,
                'nombre' => $paciente->user->name,
                'email' => $paciente->user->email,
                'nivel_estres_actual' => $paciente->nivel_estres_actual,
                'sexo' => $paciente->sexo,
                'edad' => $paciente->edad,
            ],
            'grafico_estres' => [
                'labels' => $labels,
                'data' => $valores,
            ],
            'stats' => [
                'animo_actual' => $paciente->nivel_estres_actual > 26 ? 'Alto' : ($paciente->nivel_estres_actual > 21 ? 'Moderado' : 'Bajo'),
                'mejora_porcentaje' => 15,
                'total_sesiones' => $totalSesiones,
                'proxima_sesion' => $proximaSesion ? $proximaSesion->fecha . ' ' . $proximaSesion->hora : null,
                'tareas_completadas_porcentaje' => $porcentajeGlobal,
                'total_tareas' => $totalActividades
            ],
            'modulos' => $actividades->map(function ($progreso) {
                return [
                    'id' => $progreso->id,
                    'nombre' => $progreso->actividad->nombre,
                    'progreso' => $progreso->progreso_porcentaje,
                    'estado' => $progreso->estado,
                    'fecha_actualizacion' => $progreso->updated_at->diffForHumans(),
                ];
            }),
            'actividad_reciente' => $actividades->take(3)->map(function ($progreso) {
                return [
                    'id' => $progreso->id,
                    'nombre' => $progreso->actividad->nombre,
                    'estado' => $progreso->estado,
                    'fecha_actualizacion' => $progreso->updated_at->diffForHumans(),
                    'progreso' => $progreso->progreso_porcentaje,
                ];
            }),
        ]);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->input('role') === 'psicologo') {
            $paciente = Paciente::findOrFail($id);
            $paciente->psicologo_id = auth()->user()->psicologo->id;
            $paciente->save();

            return response()->json(['message' => 'Paciente asignado correctamente']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
