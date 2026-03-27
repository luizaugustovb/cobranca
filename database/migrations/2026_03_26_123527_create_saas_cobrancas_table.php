<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_cobrancas', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('tenant_id')->constrained(); // Link para o escritório cobrado
            $blueprint->string('asaas_id')->nullable(); // ID da cobrança lá no Asaas do SaaS
            $blueprint->decimal('valor', 15, 2);
            $blueprint->date('vencimento');
            $blueprint->string('status')->default('pendente'); // pendente, pago, vencido, cancelado
            $blueprint->timestamp('data_pagamento')->nullable();
            $blueprint->string('url_boleto')->nullable();
            $blueprint->string('url_pix')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_cobrancas');
    }
};
