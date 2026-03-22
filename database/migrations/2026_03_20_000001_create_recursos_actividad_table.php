<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recursos_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->enum('tipo', ['audio', 'video', 'imagen']);
            $table->string('titulo')->nullable();
            $table->string('blob_name', 500);
            $table->string('contenedor', 100);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->unsignedInteger('duracion_segundos')->nullable();
            $table->unsignedInteger('orden')->default(1);
            $table->timestamps();

            $table->index(['actividad_id', 'tipo']);
            $table->index(['actividad_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recursos_actividad');
    }
};
