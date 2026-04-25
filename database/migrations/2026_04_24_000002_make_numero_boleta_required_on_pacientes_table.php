<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill existing null values to allow NOT NULL migration safely.
        DB::table('pacientes')
            ->whereNull('numero_boleta')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($paciente) {
                DB::table('pacientes')
                    ->where('id', $paciente->id)
                    ->update([
                        'numero_boleta' => str_pad((string) $paciente->id, 10, '0', STR_PAD_LEFT),
                    ]);
            });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('numero_boleta', 10)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('numero_boleta', 10)->nullable()->change();
        });
    }
};
