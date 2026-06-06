<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('respuestas_test')) {
            Schema::drop('respuestas_test');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('respuestas_test')) {
            Schema::create('respuestas_test', function (Blueprint $table) {
                $table->id();
                $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
                $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
                $table->date('fecha_realizacion');
                $table->float('respuesta_total');
                $table->timestamps();
            });
        }
    }
};
