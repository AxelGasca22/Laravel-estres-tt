<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaTest extends Model
{
    protected $table = 'respuestas_test';

    protected $fillable = [
        'paciente_id',
        'test_id',
        'fecha_realizacion',
        'respuesta_total',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
}
