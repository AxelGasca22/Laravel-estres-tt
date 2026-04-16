<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sesion extends Model
{
    use SoftDeletes;

    protected $table = 'sesiones';

    protected $fillable = [
        'psicologo_id',
        'paciente_id',
        'fecha',
        'hora',
        'tipo_sesion',
        'observaciones',
    ];

    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}
