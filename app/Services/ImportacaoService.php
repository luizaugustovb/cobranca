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
     *
     * @return array [importados, erros, erroDetalhe]
     */
    public function process(Importacao $importacao): array
    {
        try {
            $importacao->update(['status' => 'processando']);

            $importer = new CobrancaImport($importacao->tenant_id, $importacao->user_id);

            Excel::import($importer, storage_path('app/' . $importacao->arquivo));

            $importacao->update([
                'status'       => $importer->erros > 0 && $importer->importados === 0 ? 'erro' : 'concluido',
                'processados'  => $importer->importados,
            ]);

            return [$importer->importados, $importer->erros, $importer->erroDetalhe];

        } catch (\Exception $e) {
            $importacao->update(['status' => 'erro']);
            \Log::error("Erro na importacao [ID: {$importacao->id}]: " . $e->getMessage());
            return [0, 1, [$e->getMessage()]];
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
