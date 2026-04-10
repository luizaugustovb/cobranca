<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Devedor;
use Illuminate\Http\Request;

class BotController extends Controller
{
    /**
     * Consulta os dados de um devedor pelo CPF ou CNPJ para o Robô da Viicio.
     * Aceita os campos: cpf, cnpj ou documento (qualquer um contendo apenas dígitos).
     */
    public function consultarDocumento(Request $request)
    {
        // Segurança: verifica token de API
        $tokenSistema  = config('services.bot.api_token');
        $tokenRecebido = $request->header('Authorization');

        if (!$tokenSistema || "Bearer {$tokenSistema}" !== $tokenRecebido) {
            return response()->json(['success' => false, 'message' => 'Token de API inválido ou não autorizado.'], 403);
        }

        // Aceita campo "cpf", "cnpj" ou "documento" — remove qualquer não-dígito
        $raw      = $request->input('documento') ?? $request->input('cpf') ?? $request->input('cnpj') ?? '';
        $documento = preg_replace('/[^0-9]/', '', $raw);

        if (empty($documento)) {
            return response()->json(['success' => false, 'message' => 'CPF ou CNPJ não informado.'], 400);
        }

        // Detecta o tipo pelo número de dígitos
        $tipo = match(strlen($documento)) {
            11 => 'CPF',
            14 => 'CNPJ',
            default => null,
        };

        if ($tipo === null) {
            return response()->json([
                'success' => false,
                'message' => 'Documento inválido. Informe um CPF (11 dígitos) ou CNPJ (14 dígitos).'
            ], 400);
        }

        // Busca o devedor ignorando formatação (pontos, traços, barras)
        $devedor = Devedor::where(function ($q) use ($documento) {
                $q->where('cpf_cnpj', $documento)
                  ->orWhere('cpf_cnpj', 'LIKE', "%{$documento}%");
            })
            ->with(['titulos' => function ($q) {
                $q->where('status', 'aberto');
            }])
            ->first();

        if (!$devedor) {
            return response()->json([
                'success' => false,
                'found'   => false,
                'tipo'    => $tipo,
                'message' => "Nenhum registro encontrado para este {$tipo}.",
            ]);
        }

        // Usa o accessor valor_total (principal + juros + multa + honorários - desconto)
        $totalDebito = $devedor->titulos->sum(fn ($t) => $t->valor_total);
        $qtdTitulos  = $devedor->titulos->count();

        $mensagem = $qtdTitulos > 0
            ? "Olá *{$devedor->nome}*, identificamos *{$qtdTitulos} título(s)* em aberto totalizando *R\$ "
              . number_format($totalDebito, 2, ',', '.') . "*. Entre em contato para negociar suas dívidas."
            : "Olá *{$devedor->nome}*, não encontramos débitos em aberto em seu cadastro. 🎉";

        return response()->json([
            'success'          => true,
            'found'            => true,
            'tipo'             => $tipo,
            'nome'             => $devedor->nome,
            'total_titulos'    => $qtdTitulos,
            'valor_total'      => round($totalDebito, 2),
            'valor_formatado'  => 'R$ ' . number_format($totalDebito, 2, ',', '.'),
            'status'           => $totalDebito > 0 ? 'Inadimplente' : 'Regular',
            'whatsapp'         => $devedor->telefone,
            'message'          => $mensagem,
        ]);
    }

    /**
     * Alias mantido por compatibilidade (redireciona para consultarDocumento).
     */
    public function consultarCpf(Request $request)
    {
        return $this->consultarDocumento($request);
    }
}
