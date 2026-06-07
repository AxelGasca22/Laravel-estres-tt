<?php

namespace App\Http\Controllers;

use App\Models\ProgresoActividad;
use App\Models\Psicologo;
use App\Models\Calificacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'month');
        $selectedYm = $request->query('ym');

        try {
            $baseDate = is_string($selectedYm) && trim($selectedYm) !== ''
                ? Carbon::createFromFormat('Y-m', trim($selectedYm))
                : Carbon::now();
        } catch (\Throwable $e) {
            $baseDate = Carbon::now();
        }

        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth = $baseDate->copy()->endOfMonth();
        $psicologos = Psicologo::with('user')->get();

        $estadisticas = $psicologos->map(function ($psicologo) use ($startOfMonth, $endOfMonth, $period) {

            $pacientesIds = $psicologo->pacientes()->pluck('id');

            // Pacientes únicos con actividades realizadas.
            // Se considera realizada si está completada o en 100%.
            $queryActividades = ProgresoActividad::whereIn('paciente_id', $pacientesIds)
                ->where(function ($query) {
                    $query->where('estado', 'completado')
                        ->orWhere('progreso_porcentaje', '>=', 100);
                });

            if ($period === 'month') {
                $queryActividades->whereBetween('updated_at', [$startOfMonth, $endOfMonth]);
            }

            $pacientesConActividades = $queryActividades
                ->distinct('paciente_id')
                ->count('paciente_id');

            // Test contestados por sus pacientes.
            // Por defecto se devuelve histórico; si period=month, solo el mes actual.
            $queryTests = Calificacion::whereIn('paciente_id', $pacientesIds);

            if ($period === 'month') {
                $queryTests->whereBetween('fecha_realizacion', [
                    $startOfMonth->toDateString(),
                    $endOfMonth->toDateString(),
                ]);
            }

            $cuestionariosContestados = $queryTests
                ->count();

            return [
                'id' => $psicologo->id,
                'nombre_psicologo' => $psicologo->user->name,
                'pacientes_activos_mes' => $pacientesConActividades,
                'cuestionarios_mes' => $cuestionariosContestados,
            ];
        });

        return response()->json([
            'periodo' => $period === 'month' ? 'mes_actual' : 'historico',
            'mes_actual' => $startOfMonth->translatedFormat('F Y'),
            'ym' => $startOfMonth->format('Y-m'),
            'data' => $estadisticas
        ]);
    }
}
