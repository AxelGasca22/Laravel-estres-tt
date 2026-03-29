<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\RecursoActividad;
use Illuminate\Database\Seeder;

class RecursoActividadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videosPaisajes = [
            'cascada1.mp4',
            'desierto1.mp4',
            'hojas1.mp4',
            'hojas2.mp4',
            'monte1.mp4',
            'monte2.mp4',
            'palmeras1.mp4',
            'playa1.mp4',
        ];

        $actividadesRespiracion = Actividad::where('tipo', 'respiracion')
            ->orderBy('id')
            ->get();

        if ($actividadesRespiracion->isEmpty()) {
            return;
        }

        foreach ($actividadesRespiracion as $index => $actividad) {
            $blobName = $videosPaisajes[$index % count($videosPaisajes)];

            RecursoActividad::updateOrCreate(
                [
                    'actividad_id' => $actividad->id,
                    'tipo' => 'video',
                    'orden' => 1,
                ],
                [
                    'titulo' => 'Video de fondo - ' . $actividad->nombre,
                    'blob_name' => $blobName,
                    'contenedor' => 'paisajes',
                    'mime_type' => 'video/mp4',
                ]
            );
        }
    }
}
