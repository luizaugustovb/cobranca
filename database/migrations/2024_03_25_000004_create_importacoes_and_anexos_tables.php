<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('arquivo');
            $table->string('tipo'); // XLSX, CSV, PDF
            $table->string('status')->default('pendente'); // pendente, processando, concluido, erro
            $table->integer('total')->default(0);
            $table->integer('processados')->default(0);
            $table->integer('erros')->default(0);
            $table->timestamps();
        });

        Schema::create('importacao_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacao_id')->constrained('importacoes')->onDelete('cascade');
            $table->integer('linha');
            $table->json('dados')->nullable();
            $table->string('status')->default('sucesso'); // sucesso, falha
            $table->text('erros')->nullable();
            $table->timestamps();
        });

        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->morphs('anexavel'); // polimorfico (titulos, acordos, devedores)
            $table->string('nome');
            $table->string('caminho');
            $table->string('tipo')->nullable();
            $table->integer('tamanho')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anexos');
        Schema::dropIfExists('importacao_itens');
        Schema::dropIfExists('importacoes');
    }
};
