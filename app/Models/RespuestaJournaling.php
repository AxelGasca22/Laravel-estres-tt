<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaJournaling extends Model
{
    protected $table = 'respuestas_journaling';

    protected $fillable = [
        'paciente_id',
        'actividad_id',
        'titulo',
        'respuesta',
        'nivel_estres',
        'estado_animo',
        'visible_psicologo',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}
