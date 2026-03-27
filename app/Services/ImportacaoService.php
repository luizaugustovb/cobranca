<?php

namespace App\Services;

use App\Models\Importacao;
use App\Models\ImportacaoItem;
use App\Imports\CobrancaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportacaoService
{
    /**
     * Processar um arquivo importado
     */
    public function process(Importacao $importacao)
    {
        try {
            $importacao->update(['status' => 'processando']);
            
            // Aqui chamamos o import
            Excel::import(new CobrancaImport($importacao->tenant_id, $importacao->user_id), $importacao->arquivo);
            
            $importacao->update([
                'status' => 'concluido',
                'processados' => $importacao->total, // Exemplo simplificado
            ]);

            return true;
        } catch (\Exception $e) {
            $importacao->update(['status' => 'erro']);
            \Log::error("Erro na importacao [ID: {$importacao->id}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Criar registro inicial de importação
     */
    public function create(array $data)
    {
        return Importacao::create([
            'tenant_id' => $data['tenant_id'],
            'user_id' => $data['user_id'],
            'arquivo' => $data['arquivo'],
            'tipo' => $data['tipo'],
            'status' => 'pendente'
        ]);
    }
}
