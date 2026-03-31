<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoActividad extends Model
{
    protected $table = 'recursos_actividad';

    protected $fillable = [
        'actividad_id',
        'tipo',
        'titulo',
        'blob_name',
        'contenedor',
        'mime_type',
        'tamano_bytes',
        'duracion_segundos',
        'orden',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}
