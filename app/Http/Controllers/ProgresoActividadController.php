<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Paciente;
use App\Models\ProgresoActividad;
use Illuminate\Http\Request;

class ProgresoActividadController extends Controller
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

    private function resolveMediaAsset(Actividad $actividad): array
    {
        $activityType = strtolower((string) ($actividad->tipo ?? ''));
        $preferredResourceType = (str_contains($activityType, 'meditaci') || str_contains($activityType, 'meditacion'))
            ? 'audio'
            : 'video';

        $resource = $actividad->recursos
            ->where('tipo', $preferredResourceType)
            ->sortBy('orden')
            ->first();

        if (! $resource) {
            $fallbackType = $preferredResourceType === 'audio' ? 'video' : 'audio';
            $resource = $actividad->recursos
                ->where('tipo', $fallbackType)
                ->sortBy('orden')
                ->first();
        }

        if (! $resource || empty($resource->blob_name)) {
            return [
                'url' => null,
                'type' => null,
            ];
        }

        $blobName = trim((string) $resource->blob_name);
        if (filter_var($blobName, FILTER_VALIDATE_URL)) {
            return [
                'url' => $blobName,
                'type' => $resource->tipo,
            ];
        }

        $baseUrl = $this->resolveAzureBaseUrl();
        if (empty($baseUrl)) {
            return [
                'url' => null,
                'type' => $resource->tipo,
            ];
        }

        $container = trim((string) ($resource->contenedor ?? ''), '/');
        $blobPath = ltrim($blobName, '/');

        if ($container === '') {
            return [
                'url' => null,
                'type' => $resource->tipo,
            ];
        }

        if (str_ends_with($baseUrl, '/' . $container)) {
            $blobUrl = "$baseUrl/$blobPath";
            return [
                'url' => $this->signBlobUrl($blobUrl, $container, $blobPath),
                'type' => $resource->tipo,
            ];
        }

        $blobUrl = "$baseUrl/$container/$blobPath";
        return [
            'url' => $this->signBlobUrl($blobUrl, $container, $blobPath),
            'type' => $resource->tipo,
        ];
    }

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
        $progresos = ProgresoActividad::with('actividad.recursos')
            ->where('paciente_id', $paciente->id)
            ->get();

        $actividadesAsignadas = $progresos->map(function ($progreso) {
            if (!$progreso->actividad) {
                return null;
            }

            $media = $this->resolveMediaAsset($progreso->actividad);
            $mediaUrl = $media['url'] ?? null;
            $mediaType = $media['type'] ?? null;

            return [
                'progreso_id' => $progreso->id,
                'id' => $progreso->actividad->id,
                'titulo' => $progreso->actividad->nombre,
                'descripcion' => $progreso->actividad->descripcion,
                'tipo' => $progreso->actividad->tipo,
                'video_url' => $mediaUrl,
                'audio_url' => $mediaType === 'audio' ? $mediaUrl : null,
                'media_url' => $mediaUrl,
                'media_type' => $mediaType,
                'modulo' => $progreso->actividad->modulo,
                'estado' => $progreso->estado,
                'porcentaje' => $progreso->progreso_porcentaje,
                'fecha' => $progreso->fecha,
                'updated_at' => optional($progreso->updated_at)->toIso8601String(),
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
        $progreso = ProgresoActividad::with('actividad.recursos')
            ->where('paciente_id', $paciente->id)
            ->where('id', $id)
            ->first();

        if (!$progreso) {
            return response()->json([
                'success' => false,
                'message' => 'El progreso de actividad no fue encontrado.'
            ], 404);
        }

        $media = $this->resolveMediaAsset($progreso->actividad);
        $mediaUrl = $media['url'] ?? null;
        $mediaType = $media['type'] ?? null;

        return response()->json([
            'success' => true,
            'data' => [
                'progreso_id' => $progreso->id,
                'id' => $progreso->actividad->id,
                'titulo' => $progreso->actividad->nombre,
                'descripcion' => $progreso->actividad->descripcion,
                'tipo' => $progreso->actividad->tipo,
                'video_url' => $mediaUrl,
                'audio_url' => $mediaType === 'audio' ? $mediaUrl : null,
                'media_url' => $mediaUrl,
                'media_type' => $mediaType,
                'modulo' => $progreso->actividad->modulo,
                'estado' => $progreso->estado,
                'porcentaje' => $progreso->progreso_porcentaje,
                'fecha' => $progreso->fecha,
                'updated_at' => optional($progreso->updated_at)->toIso8601String(),
                'tiempo_estimado_min' => $progreso->actividad->tiempo_estimado_min,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $paciente = Paciente::where('user_id', $user->id)->first();

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'El perfil de paciente no fue encontrado.'
            ], 404);
        }

        $validated = $request->validate([
            'porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'estado' => ['nullable', 'string', 'in:pendiente,en_progreso,completado'],
        ]);

        $progreso = ProgresoActividad::where('paciente_id', $paciente->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('actividad_id', $id);
            })
            ->first();

        if (!$progreso) {
            return response()->json([
                'success' => false,
                'message' => 'El progreso de actividad no fue encontrado.'
            ], 404);
        }

        $porcentaje = array_key_exists('porcentaje', $validated)
            ? (float) $validated['porcentaje']
            : 100.0;

        $estado = $validated['estado'] ?? null;
        if ($estado === null) {
            $estado = $porcentaje >= 100 ? 'completado' : 'en_progreso';
        }

        $progreso->progreso_porcentaje = $porcentaje;
        $progreso->estado = $estado;
        $progreso->fecha = now()->toDateString();
        $progreso->save();

        return response()->json([
            'success' => true,
            'message' => 'Progreso de actividad actualizado.',
            'data' => [
                'progreso_id' => $progreso->id,
                'actividad_id' => $progreso->actividad_id,
                'porcentaje' => $progreso->progreso_porcentaje,
                'estado' => $progreso->estado,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
