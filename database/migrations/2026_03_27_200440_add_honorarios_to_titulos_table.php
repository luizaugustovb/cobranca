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
        Schema::table('titulos', function (Blueprint $table) {
            $table->decimal('honorarios', 12, 2)->default(0)->after('desconto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titulos', function (Blueprint $table) {
            $table->dropColumn('honorarios');
        });
    }
};
