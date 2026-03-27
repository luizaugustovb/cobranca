<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->string('nome');
            $table->string('matricula')->nullable();
            $table->string('curso')->nullable();
            $table->timestamps();
        });

        Schema::create('titulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->string('numero')->index();
            $table->decimal('valor_original', 12, 2);
            $table->decimal('juros', 12, 2)->default(0);
            $table->decimal('multa', 12, 2)->default(0);
            $table->decimal('desconto', 12, 2)->default(0);
            $table->date('vencimento');
            $table->string('status')->default('aberto'); // aberto, pago, vencido, acordo
            $table->timestamps();
        });

        Schema::create('status_cobranca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('titulo_id')->constrained('titulos')->onDelete('cascade');
            $table->string('status');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_cobranca');
        Schema::dropIfExists('titulos');
        Schema::dropIfExists('alunos');
    }
};
