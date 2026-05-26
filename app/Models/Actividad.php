<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'tiempo_estimado_min',
        'modulo',
        'categoria_id',
        'paciente_id',
    ];

    protected $casts = [
        'tiempo_estimado_min' => 'float',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function progresos()
    {
        return $this->hasMany(ProgresoActividad::class, 'actividad_id');
    }

    public function recursos()
    {
        return $this->hasMany(RecursoActividad::class, 'actividad_id');
    }

    public function respuestasJournaling()
    {
        return $this->hasMany(RespuestaJournaling::class);
    }
}
