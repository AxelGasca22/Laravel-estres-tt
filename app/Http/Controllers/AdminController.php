<?php

namespace App\Http\Controllers;

use App\Models\ProgresoActividad;
use App\Models\Psicologo;
use App\Models\HistorialCalificacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $psicologos = Psicologo::with('user')->get();

        $estadisticas = $psicologos->map(function ($psicologo) use ($startOfMonth, $endOfMonth) {
            
            $pacientesIds = $psicologo->pacientes()->pluck('id');

            // Pacientes únicos con actividades realmente realizadas (estado completado)
            // durante el mes actual (por fecha de actualización).
            $pacientesConActividades = ProgresoActividad::whereIn('paciente_id', $pacientesIds)
                ->where('estado', 'completado')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->distinct('paciente_id')
                ->count('paciente_id');

            // Test contestados del mes: usar historial para contar intentos reales.
            $cuestionariosContestados = HistorialCalificacion::whereHas('calificacion', function ($query) use ($pacientesIds) {
                    $query->whereIn('paciente_id', $pacientesIds);
                })
                ->whereBetween('fecha', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->count();

            return [
                'id' => $psicologo->id,
                'nombre_psicologo' => $psicologo->user->name,
                'pacientes_activos_mes' => $pacientesConActividades,
                'cuestionarios_mes' => $cuestionariosContestados,
            ];
        });

        return response()->json([
            'mes_actual' => Carbon::now()->translatedFormat('F'), 
            'data' => $estadisticas
        ]);
    }
}
