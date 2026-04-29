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
        Schema::table('clientes', function (Blueprint $table) {
            // % IPCA mensal equivalente aplicado ao valor original (ex: 0.38 = 0,38% a.m.)
            $table->decimal('ipca_mensal', 5, 2)->default(0)->after('honorarios_percentual');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('ipca_mensal');
        });
    }
};
