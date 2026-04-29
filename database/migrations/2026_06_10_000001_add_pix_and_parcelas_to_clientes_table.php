<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('pix_chave')->nullable()->after('endereco');
            $table->integer('max_parcelas_cartao')->default(21)->after('pix_chave');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['pix_chave', 'max_parcelas_cartao']);
        });
    }
};
