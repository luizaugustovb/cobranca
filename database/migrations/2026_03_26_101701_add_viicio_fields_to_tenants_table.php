<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('viicio_token')->nullable()->after('settings'); // Token específico do escritório no Viicio
            $table->string('viicio_company_id')->nullable()->after('viicio_token'); // ID da empresa no Viicio
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['viicio_token', 'viicio_company_id']);
        });
    }
};
