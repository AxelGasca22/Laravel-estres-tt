<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('respuestas_journaling')) {
            Schema::table('respuestas_journaling', function (Blueprint $table) {
                if (Schema::hasColumn('respuestas_journaling', 'nivel_estres')) {
                    $table->dropColumn('nivel_estres');
                }

                if (Schema::hasColumn('respuestas_journaling', 'estado_animo')) {
                    $table->dropColumn('estado_animo');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('respuestas_journaling')) {
            Schema::table('respuestas_journaling', function (Blueprint $table) {
                if (! Schema::hasColumn('respuestas_journaling', 'nivel_estres')) {
                    $table->string('nivel_estres')->nullable()->after('respuesta');
                }

                if (! Schema::hasColumn('respuestas_journaling', 'estado_animo')) {
                    $table->string('estado_animo')->nullable()->after('nivel_estres');
                }
            });
        }
    }
};
