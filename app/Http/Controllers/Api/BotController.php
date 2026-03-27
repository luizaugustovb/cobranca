<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Devedor;
use Illuminate\Http\Request;

class BotController extends Controller
{
    /**
     * Consulta os dados de um devedor pelo CPF/CNPJ para o Robô da Viicio.
     */
    public function consultarCpf(Request $request)
    {
        // Segurança: Verifica se o Token é válido
        $tokenSistemas = config('services.bot.api_token');
        $tokenRecebido = $request->header('Authorization');

        if (!$tokenSistemas || "Bearer {$tokenSistemas}" !== $tokenRecebido) {
            return response()->json(['success' => false, 'message' => 'Token de API inválido ou não autorizado.'], 403);
        }

        $cpf = preg_replace('/[^0-9]/', '', $request->cpf);

        if (empty($cpf)) {
            return response()->json(['success' => false, 'message' => 'CPF não informado ou inválido.'], 400);
        }

        // Busca o devedor (pode haver mais de um em tenants diferentes, pegamos o primeiro por simplicidade ou identificamos o tenant)
        $devedor = Devedor::where('cpf_cnpj', 'LIKE', "%{$cpf}%")->with(['titulos' => function($q) {
            $q->where('status', 'pendente');
        }])->first();

        if (!$devedor) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Nenhum registro encontrado para este CPF.'
            ]);
        }

        // Calcula débitos em aberto
        $totalDebito = $devedor->titulos->sum('valor');
        $qtdTitulos = $devedor->titulos->count();

        return response()->json([
            'success' => true,
            'found' => true,
            'nome' => $devedor->nome,
            'total_titulos' => $qtdTitulos,
            'valor_total' => $totalDebito,
            'valor_formatado' => 'R$ ' . number_format($totalDebito, 2, ',', '.'),
            'status' => ($totalDebito > 0) ? 'Inadimplente' : 'Regular',
            'whatsapp' => $devedor->telefone,
            'message' => "Olá {$devedor->nome}, detectamos {$qtdTitulos} débitos em aberto no total de R$ " . number_format($totalDebito, 2, ',', '.')
        ]);
    }
}
