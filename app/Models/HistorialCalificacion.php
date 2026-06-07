<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCalificacion extends Model
{
    protected $table = 'historial_calificaciones';

    protected $fillable = [
        'calificacion_id',
        'fecha',
        'valor',
        'categoria',
    ];

    public function calificacion()
    {
        return $this->belongsTo(Calificacion::class, 'calificacion_id');
    }
}
