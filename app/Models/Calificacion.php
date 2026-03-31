<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = [
        'paciente_id',
        'test_id',
        'calificacion_general',
        'categoria',
        'fecha_realizacion',
    ];

    protected $casts = [
        'fecha_realizacion' => 'date',
    ];

    public function historial()
    {
        return $this->hasMany(HistorialCalificacion::class);
    }
}
