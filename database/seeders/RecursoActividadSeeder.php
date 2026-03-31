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

        $audiosMeditacion = [
            'Meditacion_resp_profunda.mp3',
            'Relajacion_corporal_progresiva.mp3',
            'meditacion_ansiedad.mp3',
            'observacion_pensamientos.mp3',
            'visualizacion_lugar_seguro.mp3',
        ];

        $actividadesRespiracion = Actividad::where('tipo', 'respiracion')
            ->orderBy('id')
            ->get();

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

        $actividadesMeditacion = Actividad::whereIn('tipo', [
            'meditacion',
            'meditación',
        ])
            ->orderBy('id')
            ->get();

        foreach ($actividadesMeditacion as $index => $actividad) {
            $blobName = $audiosMeditacion[$index % count($audiosMeditacion)];

            RecursoActividad::updateOrCreate(
                [
                    'actividad_id' => $actividad->id,
                    'tipo' => 'audio',
                    'orden' => 1,
                ],
                [
                    'titulo' => 'Audio de meditación - ' . $actividad->nombre,
                    'blob_name' => $blobName,
                    'contenedor' => 'meditacion',
                    'mime_type' => 'audio/mpeg',
                ]
            );
        }
    }
}
