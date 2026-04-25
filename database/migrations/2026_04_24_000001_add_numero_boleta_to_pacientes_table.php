<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('numero_boleta', 10)->nullable()->unique()->after('semestre');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('numero_boleta');
        });
    }
};
