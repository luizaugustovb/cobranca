<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_contatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->string('tipo'); // WhatsApp, Email, Telefone
            $table->string('canal')->nullable();
            $table->text('descricao');
            $table->string('resultado')->nullable();
            $table->timestamps();
        });

        Schema::create('acordos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->decimal('valor_original', 12, 2);
            $table->decimal('desconto', 12, 2)->default(0);
            $table->decimal('valor_acordo', 12, 2);
            $table->decimal('entrada', 12, 2)->default(0);
            $table->integer('parcelas');
            $table->string('status')->default('pendente'); // pendente, ativo, cancelado, quitado
            $table->timestamps();
        });

        Schema::create('acordo_parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('acordo_id')->constrained('acordos')->onDelete('cascade');
            $table->integer('numero');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            $table->string('status')->default('aberto'); // aberto, pago, vencido
            $table->string('payment_id')->nullable(); // ID do gateway (Asaas)
            $table->timestamps();
        });

        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('acordo_id')->nullable()->constrained('acordos')->onDelete('cascade');
            $table->foreignId('parcela_id')->nullable()->constrained('acordo_parcelas')->onDelete('cascade');
            $table->decimal('valor', 12, 2);
            $table->timestamp('data_pagamento');
            $table->string('forma_pagamento')->nullable(); // pix, boleto, cartao
            $table->string('gateway_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
        Schema::dropIfExists('acordo_parcelas');
        Schema::dropIfExists('acordos');
        Schema::dropIfExists('historico_contatos');
    }
};
