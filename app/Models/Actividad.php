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
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function progresos()
    {
        return $this->hasMany(ProgresoActividad::class, 'actividad_id');
    }

    public function recursos()
    {
        return $this->hasMany(RecursoActividad::class, 'actividad_id');
    }
}
