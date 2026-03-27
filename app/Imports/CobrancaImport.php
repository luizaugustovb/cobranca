<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use App\Models\Devedor;
use App\Models\Titulo;
use Illuminate\Support\Facades\Log;

class CobrancaImport implements ToModel, WithHeadingRow, WithProgressBar
{
    private $tenantId;
    private $userId;

    public function __construct($tenantId, $userId)
    {
        $this->tenantId = $tenantId;
        $this->userId = $userId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Exemplo de mapeamento: nome, cpf_cnpj, titulo, valor, vencimento
        
        // Criar ou atualizar devedor
        $devedor = Devedor::updateOrCreate(
            ['tenant_id' => $this->tenantId, 'cpf_cnpj' => $row['cpf_cnpj']],
            ['nome' => $row['nome'], 'email' => $row['email'] ?? null, 'telefone' => $row['telefone'] ?? null]
        );

        // Criar título
        return new Titulo([
            'tenant_id' => $this->tenantId,
            'devedor_id' => $devedor->id,
            'numero' => $row['numero_titulo'] ?? uniqid(),
            'valor_original' => $row['valor'],
            'vencimento' => $row['vencimento'],
            'status' => 'aberto'
        ]);
    }
}
