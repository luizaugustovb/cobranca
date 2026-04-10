<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titulos', function (Blueprint $table) {
            $table->foreignId('acordo_id')->nullable()->constrained('acordos')->nullOnDelete()->after('devedor_id');
        });
    }

    public function down(): void
    {
        Schema::table('titulos', function (Blueprint $table) {
            $table->dropForeign(['acordo_id']);
            $table->dropColumn('acordo_id');
        });
    }
};
