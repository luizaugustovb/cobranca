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
            // % multa aplicada UMA vez no vencimento (ex: 2.00 = 2%)
            $table->decimal('multa_percentual', 5, 2)->default(0)->after('endereco');
            // % juros ao mês (ex: 1.00 = 1% a.m.)
            $table->decimal('juros_mensal', 5, 2)->default(0)->after('multa_percentual');
            // % honorários sobre o valor original (0 = usa configuração global)
            $table->decimal('honorarios_percentual', 5, 2)->default(0)->after('juros_mensal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['multa_percentual', 'juros_mensal', 'honorarios_percentual']);
        });
    }
};
