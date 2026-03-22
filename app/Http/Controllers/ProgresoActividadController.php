<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Paciente;
use App\Models\ProgresoActividad;
use Illuminate\Http\Request;

class ProgresoActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'El perfil de paciente no fue encontrado.'
            ], 404);
        }

        // Si el paciente no tiene asignadas actividades del módulo 1,
        // se crean automáticamente como pendientes.
        $actividadesModuloUno = Actividad::where('modulo', 1)->get();
        if ($actividadesModuloUno->isNotEmpty()) {
            $asignadasIds = ProgresoActividad::where('paciente_id', $paciente->id)
                ->pluck('actividad_id');

            $faltantes = $actividadesModuloUno->whereNotIn('id', $asignadasIds);

            foreach ($faltantes as $actividad) {
                ProgresoActividad::create([
                    'paciente_id' => $paciente->id,
                    'actividad_id' => $actividad->id,
                    'fecha' => now()->toDateString(),
                    'progreso_porcentaje' => 0,
                    'estado' => 'pendiente',
                ]);
            }
        }

        // Obtener el progreso con las actividades
        $progresos = ProgresoActividad::with('actividad')
            ->where('paciente_id', $paciente->id)
            ->get();

        $actividadesAsignadas = $progresos->map(function ($progreso) {
            if (!$progreso->actividad) {
                return null;
            }

            return [
                'progreso_id' => $progreso->id,
                'id' => $progreso->actividad->id,
                'titulo' => $progreso->actividad->nombre,
                'descripcion' => $progreso->actividad->descripcion,
                'tipo' => $progreso->actividad->tipo,
                'modulo' => $progreso->actividad->modulo,
                'estado' => $progreso->estado,
                'porcentaje' => $progreso->progreso_porcentaje,
                'tiempo_estimado_min' => $progreso->actividad->tiempo_estimado_min,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'data' => $actividadesAsignadas
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
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'El perfil de paciente no fue encontrado.'
            ], 404);
        }

        // Obtener el progreso específico con la actividad
        $progreso = ProgresoActividad::with('actividad')
            ->where('paciente_id', $paciente->id)
            ->where('id', $id)
            ->first();

        if (!$progreso) {
            return response()->json([
                'success' => false,
                'message' => 'El progreso de actividad no fue encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'progreso_id' => $progreso->id,
                'id' => $progreso->actividad->id,
                'titulo' => $progreso->actividad->nombre,
                'descripcion' => $progreso->actividad->descripcion,
                'tipo' => $progreso->actividad->tipo,
                'modulo' => $progreso->actividad->modulo,
                'estado' => $progreso->estado,
                'porcentaje' => $progreso->progreso_porcentaje,
                'tiempo_estimado_min' => $progreso->actividad->tiempo_estimado_min,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
