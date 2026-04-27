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
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $psicologos = Psicologo::with('user')->get();

        $estadisticas = $psicologos->map(function ($psicologo) use ($currentMonth, $currentYear) {
            
            $pacientesIds = $psicologo->pacientes()->pluck('id');

            // pacientes únicos que hicieron actividades este mes
            $pacientesConActividades = ProgresoActividad::whereIn('paciente_id', $pacientesIds)
                ->whereMonth('fecha', $currentMonth)
                ->whereYear('fecha', $currentYear)
                ->distinct('paciente_id')
                ->count('paciente_id');

            // tests respondidos este mes por sus pacientes
            $cuestionariosContestados = Calificacion::whereIn('paciente_id', $pacientesIds)
                ->whereMonth('fecha_realizacion', $currentMonth)
                ->whereYear('fecha_realizacion', $currentYear)
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
