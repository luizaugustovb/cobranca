<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicidades históricas para permitir o índice único.
        $duplicados = DB::table('titulos')
            ->select('tenant_id', 'numero', DB::raw('MIN(id) as keep_id'))
            ->groupBy('tenant_id', 'numero')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicados as $dup) {
            DB::table('titulos')
                ->where('tenant_id', $dup->tenant_id)
                ->where('numero', $dup->numero)
                ->where('id', '<>', $dup->keep_id)
                ->delete();
        }

        Schema::table('titulos', function (Blueprint $table) {
            $table->unique(['tenant_id', 'numero'], 'titulos_tenant_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('titulos', function (Blueprint $table) {
            $table->dropUnique('titulos_tenant_numero_unique');
        });
    }
};
