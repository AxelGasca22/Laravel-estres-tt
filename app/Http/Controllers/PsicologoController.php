<?php

namespace App\Http\Controllers;

use App\Http\Resources\PsicologoCollection;
use App\Mail\BienvenidoPsicologo;
use App\Models\Paciente;
use App\Models\ProgresoActividad;
use App\Models\Psicologo;
use App\Models\Sesion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PsicologoController extends Controller
{
    /**
     * Public listing for mobile app (name only).
     */
    public function publicIndex()
    {
        $psicologos = Psicologo::with('user')->get();

        return response()->json([
            'data' => $psicologos->map(function ($psicologo) {
                return [
                    'id' => $psicologo->id,
                    'name' => $psicologo->user?->name,
                ];
            })->values(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if (!$request->input('role') === 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $psicologos = Psicologo::with('user')->get();

        return (new PsicologoCollection($psicologos));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$request->input('role') === 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',            
                'regex:/[a-z]/',     
                'regex:/[A-Z]/',   
                'regex:/[0-9]/',    
                'regex:/[@$!%*?&.]/',
            ],
        ]);

        $psicologo = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => 'psicologo',
        ]);

        Psicologo::create([
            'user_id' => $psicologo->id,
            'cedula_profesional' => ''
        ]);

        $passwordPlain = $validatedData['password'];

        // ENviar correo de confirmacion al psicologo
        Mail::to($psicologo->email)->send(new BienvenidoPsicologo($psicologo, $passwordPlain));

        return response()->json(['message' => 'Psicólogo creado exitosamente']);
    }

    public function confirmarCuenta(Request $request, User $user)
    {
        // Verifica si la firma de la URL es válida y no ha expirado
        if (! $request->hasValidSignature()) {
            abort(401, 'El enlace de confirmación es inválido o ha expirado.');
        }

        // Guardar en la base de datos que ya está confirmado
        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        // Redirigir al frontend de React
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        
        return redirect()->away($frontendUrl . '/cuenta-confirmada');
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
    public function update(Request $request, string $id)
    {
        if(!$request->input('role') === 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $psicologo = Psicologo::findOrFail($id);
        $user = $psicologo->user;

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => [
                'nullable',
                'string',
                'min:8',            
                'regex:/[a-z]/',     
                'regex:/[A-Z]/',   
                'regex:/[0-9]/',    
                'regex:/[@$!%*?&.]/',
            ],
        ]);

        if (isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }
        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }
        if (isset($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }

        $user->save();

        return response()->json(['message' => 'Psicólogo actualizado exitosamente']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $psicologo = Psicologo::findOrFail($id);
        $user = $psicologo->user;

        $user->delete();
        $psicologo->delete();

        return response()->json(['message' => 'Psicólogo eliminado exitosamente']);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        // Asegurarse de que el usuario es un psicólogo
        if ($user->role !== 'psicologo' || !$user->psicologo) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $psicologoId = $user->psicologo->id;
        $now = Carbon::now();
        
        $totalPacientes = Paciente::where('psicologo_id', $psicologoId)->count();
        $pacientesNuevosMes = Paciente::where('psicologo_id', $psicologoId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // 2. Sesiones 
        $sesionesMes = Sesion::where('psicologo_id', $psicologoId)
            ->whereMonth('fecha', $now->month)
            ->whereYear('fecha', $now->year)
            ->count();
        
        $mesPasado = $now->copy()->subMonth();
        $sesionesMesPasado = Sesion::where('psicologo_id', $psicologoId)
            ->whereMonth('fecha', $mesPasado->month)
            ->whereYear('fecha', $mesPasado->year)
            ->count();

        // 3. Progreso de Actividades (Haciendo join con pacientes del psicólogo)
        $pacientesIds = Paciente::where('psicologo_id', $psicologoId)->pluck('id');
        
        // estado tiene 'completada' y 'pendiente'
        $actividadesCompletadas = ProgresoActividad::whereIn('paciente_id', $pacientesIds)
            ->where('estado', 'completado')
            ->count();
            
        $actividadesPendientes = ProgresoActividad::whereIn('paciente_id', $pacientesIds)
            ->where('estado', 'en_progreso')
            ->count();

        $totalActividades = $actividadesCompletadas + $actividadesPendientes;
        $porcentajeCompletadas = $totalActividades > 0 ? round(($actividadesCompletadas / $totalActividades) * 100) : 0;

        // 4. Niveles de Estrés 
        $estresBajo = Paciente::where('psicologo_id', $psicologoId)->where('nivel_estres_actual', '<=', 19)->count();
        $estresMedio = Paciente::where('psicologo_id', $psicologoId)->whereBetween('nivel_estres_actual', [20, 25])->count();
        $estresAlto = Paciente::where('psicologo_id', $psicologoId)->where('nivel_estres_actual', '>=', 26)->count();

        // Alertas Críticas 
        $alertasCriticas = $estresAlto;

        return response()->json([
            'pacientes' => [
                'total' => $totalPacientes,
                'nuevos_mes' => $pacientesNuevosMes
            ],
            'sesiones' => [
                'actual' => $sesionesMes,
                'mes_pasado' => $sesionesMesPasado
            ],
            'actividades' => [
                'completadas' => $actividadesCompletadas,
                'pendientes' => $actividadesPendientes,
                'porcentaje' => $porcentajeCompletadas,
                'tendencia' => 5 // calcular la tendencia real contra el mes pasado
            ],
            'estres' => [
                'bajo' => $estresBajo,
                'medio' => $estresMedio,
                'alto' => $estresAlto,
                'total' => $totalPacientes > 0 ? $totalPacientes : 1 // Para evitar división por 0 en el frontend
            ],
            'alertas_criticas' => $alertasCriticas
        ]);
    }
}
