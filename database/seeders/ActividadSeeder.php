<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActividadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriaRespiracion = Categoria::where('nombre_categoria', 'Estrés Alto')->first()
            ?? Categoria::where('nombre_categoria', 'Estrés Moderado')->first()
            ?? Categoria::first();

        if (! $categoriaRespiracion) {
            return;
        }

        $actividades = [
            [
                'nombre' => 'Respiración 4-7-8',
                'descripcion' => 'Técnica calmante: inhala 4 segundos, sostén 7 segundos y exhala 8 segundos. Se usa para bajar activación fisiológica, facilitar el sueño y reducir ansiedad aguda. Repite el ciclo 4 - 7 veces.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 3,
                'modulo' => 1,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración diafragmática',
                'descripcion' => 'Respiración abdominal: coloca una mano en pecho y otra en abdomen, inhalando para expandir el abdomen y exhalando lento. Mejora relajación y reduce tensión física. Repite el ciclo 5 - 10 veces.
                Inhala 6 segundos, exhala 6 segundos para un ritmo más lento y calmante.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 4,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración en caja (Box Breathing)',
                'descripcion' => 'Patrón 4-4-4-4: inhala 4 segundos, sostén 4, exhala 4 y sostén 4. Favorece concentración, estabilidad emocional y autorregulación en momentos de presión. Repite el ciclo 4 - 5 veces para obtener beneficios óptimos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 3,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración alterna (Nadi Shodhana)',
                'descripcion' => 'Alterna fosas nasales en cada ciclo respiratorio. Técnica tradicional para equilibrar atención y calma mental; útil para sesiones de relajación más largas.
                1. Cierra la fosa nasal derecha con el pulgar, inhala por la izquierda durante 4 segundos.
                2. Cierra la fosa nasal izquierda con el anular, sostén la respiración durante 4 segundos.
                3. Abre la fosa nasal derecha, exhala lentamente durante 4 segundos.
                4. Inhala por la fosa nasal derecha durante 4 segundos.
                5. Cierra la fosa nasal derecha, sostén durante 4 segundos.
                6. Abre la fosa nasal izquierda, exhala durante 4 segundos.
                Repite el ciclo varias veces para promover equilibrio y relajación profunda.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 6,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración coherente 5-5',
                'descripcion' => 'Ritmo constante de 5 segundos al inhalar y 5 al exhalar (aprox. 6 respiraciones por minuto). Favorece regulación del sistema nervioso y práctica diaria sostenible. Repite 3 - 5 veces.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 5,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
        ];

        foreach ($actividades as $actividad) {
            Actividad::updateOrCreate(
                ['nombre' => $actividad['nombre']],
                $actividad
            );
        }
    }
}
