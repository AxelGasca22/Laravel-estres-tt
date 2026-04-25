<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';

    protected $fillable = [
        'user_id',
        'psicologo_id',
        'edad',
        'sexo',
        'nivel_estres_actual',
        'semestre',
        'numero_boleta',
        'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class);
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class);
    }

    public function ultimaSesion()
    {
        return $this->hasOne(Sesion::class)->latestOfMany('fecha');
    }

    public function progresoActividad()
    {
        return $this->hasOne(ProgresoActividad::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function progresosActividades()
    {
        return $this->hasMany(ProgresoActividad::class, 'paciente_id');
    }
}
