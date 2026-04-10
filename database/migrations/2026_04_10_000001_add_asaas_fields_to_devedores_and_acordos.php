<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devedores', function (Blueprint $table) {
            $table->string('asaas_customer_id')->nullable()->after('telefone');
        });

        Schema::table('acordos', function (Blueprint $table) {
            $table->string('forma_pagamento')->nullable()->after('status');
            $table->string('asaas_link')->nullable()->after('forma_pagamento');
        });
    }

    public function down(): void
    {
        Schema::table('devedores', function (Blueprint $table) {
            $table->dropColumn('asaas_customer_id');
        });

        Schema::table('acordos', function (Blueprint $table) {
            $table->dropColumn(['forma_pagamento', 'asaas_link']);
        });
    }
};
