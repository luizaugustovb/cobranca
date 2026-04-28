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
        Schema::table('devedores', function (Blueprint $table) {
            $table->string('rua')->nullable()->after('telefone');
            $table->string('numero')->nullable()->after('rua');
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('cep', 9)->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('devedores', function (Blueprint $table) {
            $table->dropColumn(['rua', 'numero', 'bairro', 'cidade', 'estado', 'cep']);
        });
    }
};
