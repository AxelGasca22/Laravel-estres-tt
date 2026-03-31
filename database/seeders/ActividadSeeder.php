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

        $categoriaEjercicio = Categoria::where('nombre_categoria', 'Ejercicio Físico')->first()
            ?? Categoria::where('nombre_categoria', 'Estrés Moderado')->first()
            ?? $categoriaRespiracion;

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
            [
                'nombre' => 'Meditación respiración profunda',
                'descripcion' => 'Enfócate en una respiración lenta y consciente. Inhala por la nariz, retén suavemente y exhala largo por la boca. Repite el ciclo con atención plena para reducir activación y ansiedad.',
                'tipo' => 'meditacion',
                'tiempo_estimado_min' => 2.51,
                'modulo' => 1,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Relajación corporal progresiva',
                'descripcion' => 'Recorre el cuerpo desde los pies hasta la cabeza. Tensa y relaja cada zona durante unos segundos, observando las sensaciones y soltando tensión muscular acumulada.',
                'tipo' => 'meditacion',
                'tiempo_estimado_min' => 2.50,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Meditación para ansiedad',
                'descripcion' => 'Observa tus pensamientos sin juzgarlos y vuelve suavemente a la respiración cada vez que te distraigas. Usa frases breves de calma para estabilizar mente y cuerpo.',
                'tipo' => 'meditacion',
                'tiempo_estimado_min' => 2.13,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Observación de pensamientos',
                'descripcion' => 'Toma una postura cómoda y observa los pensamientos como si fueran nubes que pasan. No luches contra ellos: identifícalos, suéltalos y regresa al momento presente.',
                'tipo' => 'meditacion',
                'tiempo_estimado_min' => 1.45,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Visualización de lugar seguro',
                'descripcion' => 'Imagina un lugar que te transmita tranquilidad. Añade detalles visuales, sonidos y sensaciones físicas para profundizar el estado de calma y seguridad.',
                'tipo' => 'meditacion',
                'tiempo_estimado_min' => 2.26,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Caminata ligera',
                'descripcion' => 'Camina a un ritmo cómodo durante unos minutos, enfocándote en mantener un paso constante. Ideal para despejar la mente y reducir el estrés acumulado.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 10,
                'modulo' => 1,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Caminata rápida',
                'descripcion' => 'Aumenta ligeramente el ritmo de tu caminata para activar tu cuerpo. Mantén una postura erguida y respira de forma natural.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 15,
                'modulo' => 1,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Trote ligero por intervalos',
                'descripcion' => 'Alterna entre caminar y trotar suavemente. Por ejemplo, trota 1 minuto y camina 2 minutos para liberar tensión sin sobrecargar el cuerpo.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 12,
                'modulo' => 2,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Estiramiento de cuello y hombros',
                'descripcion' => 'Inclina suavemente la cabeza hacia los lados y realiza movimientos circulares con los hombros para liberar tensión acumulada.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 5,
                'modulo' => 1,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Estiramiento de espalda baja',
                'descripcion' => 'Realiza movimientos suaves inclinando el torso hacia adelante y hacia atrás para relajar la zona lumbar.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 5,
                'modulo' => 1,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Rutina básica de sentadillas',
                'descripcion' => 'Realiza sentadillas de forma controlada, manteniendo la espalda recta. Este ejercicio ayuda a liberar tensión y activar el cuerpo.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 8,
                'modulo' => 2,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Lagartijas modificadas',
                'descripcion' => 'Realiza lagartijas apoyando las rodillas si es necesario. Mantén el cuerpo alineado y ejecuta el movimiento de forma controlada.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 6,
                'modulo' => 2,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Plancha abdominal',
                'descripcion' => 'Mantén una posición de plancha durante algunos segundos, activando el abdomen y manteniendo el cuerpo recto.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 4,
                'modulo' => 2,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Cardio rápido en casa',
                'descripcion' => 'Realiza ejercicios como jumping jacks o rodillas altas durante intervalos cortos para liberar energía y reducir el estrés.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 5,
                'modulo' => 3,
                'categoria_id' => $categoriaEjercicio->id,
            ],
            [
                'nombre' => 'Desplantes alternados',
                'descripcion' => 'Da un paso al frente y baja el cuerpo formando un ángulo de 90 grados. Alterna piernas de forma controlada.',
                'tipo' => 'ejercicio',
                'tiempo_estimado_min' => 7,
                'modulo' => 2,
                'categoria_id' => $categoriaEjercicio->id,
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
