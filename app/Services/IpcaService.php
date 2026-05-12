<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de correção pelo IPCA.
 *
 * Fonte: Banco Central do Brasil — série 433 (IPCA variação mensal).
 * API: https://api.bcb.gov.br/dados/serie/bcdata.sgs.433/dados?formato=json&dataInicial=...&dataFinal=...
 *
 * Cache: 24 horas por chave de período.
 */
class IpcaService
{
    private const BCB_URL  = 'https://api.bcb.gov.br/dados/serie/bcdata.sgs.433/dados';
    private const CACHE_TTL = 86400; // 24 h em segundos

    /**
     * Retorna o IPCA acumulado (em %) entre a data de vencimento e hoje.
     *
     * Exemplo: se o período acumulou 12,5%, retorna 12.5.
     *
     * @param  Carbon|\DateTime|string  $desde  Data de início (vencimento do título)
     * @return float  Percentual acumulado (ex: 12.5 para 12,5%)
     */
    public function acumuladoDesde($desde): float
    {
        try {
            $inicio = Carbon::parse($desde)->startOfMonth();
            $hoje   = Carbon::now()->startOfMonth();

            // Título ainda dentro do prazo ou vencimento no mês atual — sem correção
            if ($inicio->greaterThanOrEqualTo($hoje)) {
                return 0.0;
            }

            $cacheKey = 'ipca_' . $inicio->format('Ym') . '_' . $hoje->format('Ym');

            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($inicio, $hoje) {
                return $this->buscarAcumulado($inicio, $hoje);
            });
        } catch (\Throwable $e) {
            Log::warning('IpcaService::acumuladoDesde falhou: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Busca os dados mensais do BCB e calcula o acumulado.
     * O produto encadeado é: ∏(1 + taxa_i/100) − 1, convertido em %.
     */
    private function buscarAcumulado(Carbon $inicio, Carbon $hoje): float
    {
        $dataInicial = $inicio->format('d/m/Y');
        // Fim = primeiro dia do mês anterior ao atual (BCB não tem o mês corrente ainda)
        $dataFinal   = $hoje->subMonth()->endOfMonth()->format('d/m/Y');

        $response = Http::timeout(10)->get(self::BCB_URL, [
            'formato'     => 'json',
            'dataInicial' => $dataInicial,
            'dataFinal'   => $dataFinal,
        ]);

        if (!$response->successful()) {
            Log::warning('IpcaService: BCB retornou HTTP ' . $response->status());
            return 0.0;
        }

        $dados = $response->json();

        if (empty($dados) || !is_array($dados)) {
            return 0.0;
        }

        // Produto encadeado: (1 + r1/100) × (1 + r2/100) × ... − 1
        $fator = 1.0;
        foreach ($dados as $item) {
            $taxa = isset($item['valor']) ? (float) str_replace(',', '.', $item['valor']) : 0.0;
            $fator *= (1 + $taxa / 100);
        }

        return round(($fator - 1) * 100, 6); // ex: 12.534567
    }
}
