<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ProgresoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActividadController extends Controller
{
    private function connectionStringValue(string $key): ?string
    {
        $connectionString = (string) config('filesystems.disks.azure.connection_string', '');
        if ($connectionString === '') {
            return null;
        }

        $parts = explode(';', $connectionString);
        foreach ($parts as $part) {
            [$k, $v] = array_pad(explode('=', $part, 2), 2, null);
            if ($k !== null && strcasecmp(trim($k), $key) === 0) {
                return $v !== null ? trim($v) : null;
            }
        }

        return null;
    }

    private function resolveAzureBaseUrl(): ?string
    {
        $configuredUrl = trim((string) config('filesystems.disks.azure.url', ''));
        $connectionString = (string) config('filesystems.disks.azure.connection_string', '');

        if ($connectionString !== '') {
            preg_match('/AccountName=([^;]+)/i', $connectionString, $accountMatch);
            preg_match('/EndpointSuffix=([^;]+)/i', $connectionString, $suffixMatch);

            $accountName = $accountMatch[1] ?? null;
            $endpointSuffix = $suffixMatch[1] ?? 'core.windows.net';

            if (! empty($accountName)) {
                return "https://{$accountName}.blob.{$endpointSuffix}";
            }
        }

        if ($configuredUrl === '') {
            return null;
        }

        return rtrim($configuredUrl, '/');
    }

    private function signBlobUrl(string $blobUrl, string $container, string $blobPath): string
    {
        $accountName = $this->connectionStringValue('AccountName');
        $accountKey = $this->connectionStringValue('AccountKey');

        if (empty($accountName) || empty($accountKey)) {
            return $blobUrl;
        }

        $signedPermissions = 'r';
        $signedStart = gmdate('Y-m-d\TH:i:s\Z', time() - 300);
        $signedExpiry = gmdate('Y-m-d\TH:i:s\Z', time() + 2 * 60 * 60);
        $signedProtocol = 'https';
        $signedVersion = '2022-11-02';
        $signedResource = 'b';

        $canonicalizedResource = sprintf(
            '/blob/%s/%s/%s',
            $accountName,
            trim($container, '/'),
            ltrim(urldecode($blobPath), '/')
        );

        $stringToSign = implode("\n", [
            $signedPermissions,
            $signedStart,
            $signedExpiry,
            $canonicalizedResource,
            '',
            '',
            $signedProtocol,
            $signedVersion,
            $signedResource,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);

        $decodedKey = base64_decode($accountKey, true);
        if ($decodedKey === false) {
            return $blobUrl;
        }

        $signature = base64_encode(
            hash_hmac('sha256', $stringToSign, $decodedKey, true)
        );

        $queryParams = http_build_query([
            'sv' => $signedVersion,
            'spr' => $signedProtocol,
            'st' => $signedStart,
            'se' => $signedExpiry,
            'sr' => $signedResource,
            'sp' => $signedPermissions,
            'sig' => $signature,
        ], '', '&', PHP_QUERY_RFC3986);

        $separator = str_contains($blobUrl, '?') ? '&' : '?';
        return $blobUrl . $separator . $queryParams;
    }

    private function resolveResourceUrl($resource): ?string
    {
        if (! $resource || empty($resource->blob_name)) {
            return null;
        }

        $blobName = trim((string) $resource->blob_name);
        if (filter_var($blobName, FILTER_VALIDATE_URL)) {
            return $blobName;
        }

        $baseUrl = $this->resolveAzureBaseUrl();
        $container = trim((string) ($resource->contenedor ?? ''), '/');

        if (! $baseUrl || $container === '') {
            return null;
        }

        $blobPath = ltrim($blobName, '/');

        if (str_ends_with($baseUrl, '/' . $container)) {
            $blobUrl = "$baseUrl/$blobPath";
            return $this->signBlobUrl($blobUrl, $container, $blobPath);
        }

        $blobUrl = "$baseUrl/$container/$blobPath";
        return $this->signBlobUrl($blobUrl, $container, $blobPath);
    }

    private function resolveAudioUrl(Actividad $actividad): ?string
    {
        $audioResource = $actividad->recursos
            ->where('tipo', 'audio')
            ->sortBy('orden')
            ->first();

        return $this->resolveResourceUrl($audioResource);
    }

    private function parseDurationToMinutes(mixed $duration): float
    {
        if ($duration === null) {
            return 1.0;
        }

        $raw = trim((string) $duration);
        if ($raw === '') {
            return 1.0;
        }

        if (preg_match('/^(\d{1,3}):(\d{1,2})$/', $raw, $matches)) {
            $minutes = (int) $matches[1];
            $seconds = min(59, (int) $matches[2]);
            return round($minutes + ($seconds / 60), 2);
        }

        if (preg_match('/^(\d+)[\.,](\d{1,2})$/', $raw, $matches)) {
            $minutes = (int) $matches[1];
            $seconds = (int) str_pad($matches[2], 2, '0', STR_PAD_RIGHT);

            if ($seconds <= 59) {
                return round($minutes + ($seconds / 60), 2);
            }
        }

        if (is_numeric($raw)) {
            $value = max(0.0, (float) $raw);
            return round($value, 2);
        }

        return 1.0;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->input('role') !== 'psicologo') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $actividades = Actividad::withCount('progresos')
            ->with(['recursos' => function ($query) {
                $query->select('id', 'actividad_id', 'tipo', 'blob_name', 'contenedor', 'orden');
            }])
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->get()
            ->map(function (Actividad $actividad) {
                return [
                    'id' => $actividad->id,
                    'nombre' => $actividad->nombre,
                    'descripcion' => $actividad->descripcion,
                    'tipo' => $actividad->tipo,
                    'tiempo_estimado_min' => $actividad->tiempo_estimado_min,
                    'modulo' => $actividad->modulo,
                    'audio_url' => $this->resolveAudioUrl($actividad),
                    'asignaciones_total' => $actividad->progresos_count,
                    'updated_at' => optional($actividad->updated_at)->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $actividades,
        ]);
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
        $actividad->tiempo_estimado_min = $this->parseDurationToMinutes($request->input('duracion'));
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
        $actividad->tiempo_estimado_min = $this->parseDurationToMinutes($request->input('duracion'));
        $actividad->modulo = (int) $request->input('modulo', 1);
        $actividad->save();

        return response()->json(['message' => 'Actividad actualizada exitosamente']);
    }

    public function updateModulo(Request $request, string $id)
    {
        if ($request->input('role') !== 'psicologo') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'modulo' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $actividad = Actividad::findOrFail($id);
        $actividad->modulo = (int) $validated['modulo'];
        $actividad->save();

        return response()->json([
            'success' => true,
            'message' => 'Módulo actualizado correctamente.',
            'data' => [
                'id' => $actividad->id,
                'modulo' => $actividad->modulo,
            ],
        ]);
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
