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
            [
                'nombre' => 'Respiración con labios fruncidos',
                'descripcion' => 'Inhala lentamente por la nariz durante 2 segundos y exhala suavemente por la boca con los labios fruncidos durante 4 segundos o más. Ayuda a desacelerar el ritmo respiratorio, recuperar control de la respiración y favorecer la relajación. Repite de 5 a 10 ciclos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 3.0,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración con exhalación prolongada',
                'descripcion' => 'Inhala de forma cómoda y exhala más lento y por más tiempo que la inhalación. Mantén un ritmo suave, sin forzar el aire. Útil para reducir tensión y activar una respuesta de calma. Practica durante 3 a 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 4.0,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración pautada',
                'descripcion' => 'Sigue un ritmo constante guiado: inhala, pausa breve si es cómodo, exhala lentamente y repite. Esta técnica ayuda a disminuir la activación física asociada al estrés y la ansiedad. Practica durante 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 5.0,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración consciente',
                'descripcion' => 'Lleva tu atención únicamente a la respiración. Observa cómo entra y sale el aire, sin intentar cambiarlo demasiado. Si aparecen pensamientos, reconócelos y vuelve suavemente a la respiración. Practica 3 a 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 5.0,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración abdominal suave',
                'descripcion' => 'Coloca una mano sobre el abdomen y permite que el vientre se suavice al inhalar. Exhala lentamente y deja que el cuerpo libere tensión. No fuerces la profundidad del aire. Repite durante 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 5.0,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración de conteo 5-5 suave',
                'descripcion' => 'Inhala suavemente contando hasta 5 y exhala contando hasta 5, sin forzar si no llegas al conteo completo. Mantén el aire fluyendo de manera cómoda. Repite durante al menos 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 5.0,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración profunda lenta',
                'descripcion' => 'Cierra los ojos e inhala lentamente por la nariz, llevando el aire hacia la parte baja de los pulmones. Exhala despacio y repite varias veces hasta percibir menor tensión corporal. Practica de 3 a 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 4.0,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración cíclica con suspiro',
                'descripcion' => 'Realiza una inhalación profunda, añade una segunda inhalación corta para llenar un poco más los pulmones y después exhala de forma larga y lenta. Enfatiza la exhalación prolongada para disminuir activación fisiológica. Repite 5 ciclos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 3.0,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Meditación centrada en la respiración',
                'descripcion' => 'Siéntate en una posición cómoda y usa la respiración como punto de atención. Puedes repetir mentalmente una palabra tranquila al exhalar. Deja pasar los pensamientos sin engancharte y vuelve a la respiración. Practica 5 a 10 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 7.0,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Respiración para manejo de ansiedad',
                'descripcion' => 'Inhala lentamente, mantén una pausa breve si resulta cómodo y exhala despacio. El objetivo no es respirar perfecto, sino recuperar sensación de control durante momentos de ansiedad o estrés. Practica de 3 a 5 minutos.',
                'tipo' => 'respiracion',
                'tiempo_estimado_min' => 4.0,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            // ─── Journaling ───────────────────────────────────────────────────
            [
                'nombre' => 'Journaling: estrés del día',
                'descripcion' => 'Responde por escrito: 1) ¿Qué situación te generó más estrés hoy? 2) ¿Qué emoción sentiste con mayor intensidad? 3) ¿Qué pensamiento apareció en ese momento? 4) ¿Qué podrías hacer para cuidarte después de esta situación?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 8.0,
                'modulo' => 1,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: identificación de emociones',
                'descripcion' => 'Responde por escrito: 1) ¿Qué emoción predomina en este momento? 2) ¿Dónde la sientes en el cuerpo? 3) ¿Qué pudo haberla provocado? 4) ¿Qué necesitas ahora para sentirte un poco mejor?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 7.0,
                'modulo' => 1,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: pensamientos automáticos',
                'descripcion' => 'Responde por escrito: 1) ¿Qué situación activó tu malestar? 2) ¿Qué pensamiento apareció de inmediato? 3) ¿Ese pensamiento es completamente cierto o hay otra forma de verlo? 4) ¿Qué pensamiento más realista podrías escribir?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 10.0,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: gratitud',
                'descripcion' => 'Responde por escrito: 1) ¿Qué tres cosas agradeces hoy? 2) ¿Qué persona, lugar o momento te dio calma? 3) ¿Qué hiciste hoy que fue valioso para ti? 4) ¿Cómo puedes repetir algo positivo mañana?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 6.0,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: descarga mental',
                'descripcion' => 'Responde por escrito: 1) ¿Qué pensamientos tienes dando vueltas en la mente? 2) ¿Cuáles son urgentes y cuáles pueden esperar? 3) ¿Qué está bajo tu control? 4) ¿Qué puedes soltar por ahora?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 8.0,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: solución de problemas',
                'descripcion' => 'Responde por escrito: 1) ¿Cuál es el problema principal? 2) ¿Qué opciones tienes para enfrentarlo? 3) ¿Cuál opción es más realista hoy? 4) ¿Cuál es el primer paso pequeño que puedes dar?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 10.0,
                'modulo' => 2,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: cierre del día',
                'descripcion' => 'Responde por escrito: 1) ¿Qué fue lo más difícil del día? 2) ¿Qué salió mejor de lo esperado? 3) ¿Qué aprendiste sobre ti hoy? 4) ¿Qué necesitas para descansar mejor esta noche?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 7.0,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: tensión corporal',
                'descripcion' => 'Responde por escrito: 1) ¿En qué parte del cuerpo sientes más tensión? 2) ¿Qué emoción podría estar relacionada? 3) ¿Qué situación pudo activar esa tensión? 4) ¿Qué acción breve puedes hacer para relajar el cuerpo?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 7.0,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: intención para mañana',
                'descripcion' => 'Responde por escrito: 1) ¿Cómo quieres sentirte mañana? 2) ¿Qué situación podría generarte estrés? 3) ¿Qué puedes preparar desde hoy? 4) Escribe una intención breve para cuidar tu bienestar.',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 6.0,
                'modulo' => 3,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: límites personales',
                'descripcion' => 'Responde por escrito: 1) ¿En qué situación sentiste que necesitabas poner un límite? 2) ¿Qué emoción apareció? 3) ¿Qué te impidió expresar ese límite? 4) ¿Cómo podrías comunicarlo de forma clara y respetuosa?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 9.0,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: preocupación vs control',
                'descripcion' => 'Responde por escrito: 1) ¿Qué preocupación ocupa más tu mente ahora? 2) ¿Qué parte de esa situación está bajo tu control? 3) ¿Qué parte no depende de ti? 4) ¿Qué acción pequeña puedes tomar hoy?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 8.0,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: diálogo interno',
                'descripcion' => 'Responde por escrito: 1) ¿Qué frase negativa te has repetido últimamente? 2) ¿Cómo te hace sentir esa frase? 3) ¿Qué evidencia existe a favor y en contra? 4) Escribe una versión más justa y amable de ese pensamiento.',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 10.0,
                'modulo' => 4,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: momento de calma',
                'descripcion' => 'Responde por escrito: 1) ¿En qué momento reciente sentiste tranquilidad? 2) ¿Qué elementos ayudaron a que te sintieras así? 3) ¿Cómo reaccionó tu cuerpo? 4) ¿Cómo podrías crear otro momento parecido esta semana?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 6.0,
                'modulo' => 5,
                'categoria_id' => $categoriaRespiracion->id,
            ],
            [
                'nombre' => 'Journaling: avance personal',
                'descripcion' => 'Responde por escrito: 1) ¿Qué situación antes te costaba más manejar? 2) ¿Qué has hecho diferente últimamente? 3) ¿Qué avance pequeño reconoces en ti? 4) ¿Qué hábito te gustaría seguir fortaleciendo?',
                'tipo' => 'journaling',
                'tiempo_estimado_min' => 8.0,
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
