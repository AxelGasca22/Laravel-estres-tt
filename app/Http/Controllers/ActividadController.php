<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ProgresoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->input('role') !== 'psicologo') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $actividad = new Actividad();
        $actividad->nombre = $request->input('nombre');
        $actividad->descripcion = $request->input('descripcion');
        $actividad->tipo = $request->input('categoria');
        $actividad->tiempo_estimado_min = $request->input('duracion');
        $actividad->modulo = (int) $request->input('modulo', 1);
        $actividad->categoria_id = '1';
        $actividad->save();

        $progresoActividad = new ProgresoActividad();
        $progresoActividad->actividad_id = $actividad->id;
        $progresoActividad->paciente_id = $request->input('paciente_id');
        $progresoActividad->fecha = Carbon::now()->format('Y-m-d');
        $progresoActividad->progreso_porcentaje = 0;
        $progresoActividad->estado = 'en_progreso';
        $progresoActividad->save();

        return response()->json(['message' => 'Actividad creada exitosamente', 'id' => $actividad->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if ($request->input('role') !== 'psicologo') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $actividadProgreso = ProgresoActividad::findorFail($id);
        $actividad = Actividad::findorFail($actividadProgreso->actividad_id);
        return response()->json($actividad);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->input('role') !== 'psicologo') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $actividadProgreso = ProgresoActividad::findorFail($id);
        $actividad = Actividad::findorFail($actividadProgreso->actividad_id);
        $actividad->nombre = $request->input('nombre');
        $actividad->descripcion = $request->input('descripcion');
        $actividad->tipo = $request->input('categoria');
        $actividad->tiempo_estimado_min = $request->input('duracion');
        $actividad->modulo = (int) $request->input('modulo', 1);
        $actividad->save();

        return response()->json(['message' => 'Actividad actualizada exitosamente']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        if (request()->input('role') !== 'psicologo') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $actividadProgreso = ProgresoActividad::findorFail($id);
        $actividad = Actividad::findorFail($actividadProgreso->actividad_id);
        $actividadProgreso->delete();
        $actividad->delete();
        return response()->json(['message' => 'Actividad eliminada exitosamente']);
    }
}
