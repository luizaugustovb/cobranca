<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Integrations\AsaasService;
use App\Models\Acordo;
use App\Models\AcordoParcela;
use App\Models\Cliente;
use App\Models\Devedor;
use App\Models\Setting;
use App\Models\Titulo;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcordoController extends Controller
{
    public function index(Request $request)
    {
        $clientes  = Cliente::orderBy('nome')->get();
        $clienteId = $request->get('cliente_id');
        $busca     = trim($request->get('busca', ''));

        $acordos = Acordo::with('devedor.cliente')
            ->when($clienteId, fn($q) => $q->whereHas('devedor', fn($dq) => $dq->where('cliente_id', $clienteId)))
            ->when($busca, fn($q) => $q->whereHas('devedor', fn($dq) => $dq->where('nome', 'like', "%{$busca}%")))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('tenant.acordos.index', compact('acordos', 'clientes', 'clienteId', 'busca'));
    }

    public function create(Request $request)
    {
        $devedorId = $request->get('devedor');
        $devedor = Devedor::with(['titulos' => function ($q) {
            $q->where('status', 'aberto');
        }, 'cliente'])->findOrFail($devedorId);

        $totalPrincipal  = $devedor->titulos->sum('valor_original');
        $totalJuros      = $devedor->titulos->sum('juros');
        $totalMulta      = $devedor->titulos->sum('multa');
        $totalHonorarios = $devedor->titulos->sum('honorarios');
        $totalOriginal   = $totalPrincipal + $totalJuros + $totalMulta + $totalHonorarios;

        $maxParcelasCartao = (int) ($devedor->cliente->max_parcelas_cartao ?? 21);
        $pixChave          = $devedor->cliente->pix_chave ?? '';

        return view('tenant.acordos.create', compact(
            'devedor',
            'totalOriginal',
            'totalPrincipal',
            'totalJuros',
            'totalMulta',
            'totalHonorarios',
            'maxParcelasCartao',
            'pixChave'
        ));
    }

    public function store(Request $request)
    {
        // Validação prévia para calcular limite de parcelas
        $request->validate([
            'devedor_id'      => 'required|exists:devedores,id',
            'forma_pagamento' => 'required|string|in:BOLETO,PIX,CREDIT_CARD,UNDEFINED',
        ]);

        $devedor = Devedor::with('cliente')->findOrFail($request->devedor_id);

        $maxParcelasCartao = (int) ($devedor->cliente->max_parcelas_cartao ?? 21);
        $maxParcelas = match ($request->forma_pagamento) {
            'PIX'         => 1,
            'CREDIT_CARD' => min(21, $maxParcelasCartao),
            default       => 120,
        };

        $request->validate([
            'valor_original'      => 'required|numeric',
            'desconto'            => 'required|numeric|min:0',
            'valor_acordo'        => 'required|numeric',
            'entrada'             => 'required|numeric|min:0',
            'parcelas'            => "required|integer|min:1|max:{$maxParcelas}",
            'vencimento_primeira' => 'required|date|after_or_equal:today',
        ]);

        return DB::transaction(function () use ($request, $devedor) {
            $isPix    = $request->forma_pagamento === 'PIX';
            $pixChave = $isPix ? ($devedor->cliente->pix_chave ?? null) : null;

            // --- Integração Asaas (somente para BOLETO, CREDIT_CARD, UNDEFINED) ---
            $asaasToken      = Setting::where('key', 'asaas_token')->value('value');
            $asaasCustomerId = $devedor->asaas_customer_id;
            $asaasLink       = null;

            if (!$isPix && $asaasToken) {
                try {
                    $asaas = new AsaasService($asaasToken);

                    if (!$asaasCustomerId) {
                        $customer = $asaas->createCustomer([
                            'nome'      => $devedor->nome,
                            'documento' => preg_replace('/[^0-9]/', '', $devedor->cpf_cnpj),
                            'email'     => $devedor->email,
                            'telefone'  => $devedor->telefone,
                        ]);
                        $asaasCustomerId = $customer['id'] ?? null;
                        if ($asaasCustomerId) {
                            $devedor->update(['asaas_customer_id' => $asaasCustomerId]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Erro Asaas ao criar cliente: ' . $e->getMessage());
                }
            }

            // 1. Criar Acordo
            $acordo = Acordo::create([
                'tenant_id'       => auth()->user()->tenant_id,
                'devedor_id'      => $devedor->id,
                'valor_original'  => $request->valor_original,
                'desconto'        => $request->desconto,
                'valor_acordo'    => $request->valor_acordo,
                'entrada'         => $request->entrada,
                'parcelas'        => $request->parcelas,
                'status'          => 'ativo',
                'forma_pagamento' => $request->forma_pagamento,
            ]);

            // 2. Marcar títulos como negociados
            Titulo::where('devedor_id', $devedor->id)
                ->where('status', 'aberto')
                ->update(['status' => 'negociado', 'acordo_id' => $acordo->id]);

            // 3. Gerar Parcelas
            $valorParcela   = round(($request->valor_acordo - $request->entrada) / $request->parcelas, 2);
            $dataVencimento = Carbon::parse($request->vencimento_primeira);

            for ($i = 1; $i <= $request->parcelas; $i++) {
                $paymentId = null;

                // PIX: não cria cobranças no Asaas
                if (!$isPix && $asaasToken && $asaasCustomerId) {
                    try {
                        $asaas   = new AsaasService($asaasToken);
                        $payment = $asaas->createPayment([
                            'customer_id' => $asaasCustomerId,
                            'type'        => $request->forma_pagamento,
                            'value'       => $valorParcela,
                            'due_date'    => $dataVencimento->copy()->addMonths($i - 1)->format('Y-m-d'),
                            'description' => "Acordo #{$acordo->id} — Parcela {$i}/{$request->parcelas} — {$devedor->nome}",
                            'reference'   => "acordo_{$acordo->id}_p{$i}",
                        ]);
                        $paymentId = $payment['id'] ?? null;
                        if ($i === 1) {
                            $asaasLink = $payment['invoiceUrl'] ?? null;
                        }
                    } catch (\Exception $e) {
                        Log::error("Erro Asaas parcela {$i}: " . $e->getMessage());
                    }
                }

                AcordoParcela::create([
                    'tenant_id'  => auth()->user()->tenant_id,
                    'acordo_id'  => $acordo->id,
                    'numero'     => $i,
                    'valor'      => $valorParcela,
                    'vencimento' => $dataVencimento->copy()->addMonths($i - 1),
                    'status'     => 'aberto',
                    'payment_id' => $paymentId,
                ]);
            }

            // Salvar link: para PIX → chave PIX; para outros → link Asaas
            if ($isPix && $pixChave) {
                $acordo->update(['asaas_link' => $pixChave]);
            } elseif ($asaasLink) {
                $acordo->update(['asaas_link' => $asaasLink]);
            }

            // 4. Enviar WhatsApp via Viício
            $this->enviarWhatsAppAcordo($devedor, $acordo, $asaasLink, $pixChave);

            if ($isPix) {
                $msg = 'Acordo formalizado! O devedor deve pagar via PIX na chave: ' . ($pixChave ?: '(não configurada)');
            } elseif ($asaasToken && $asaasCustomerId) {
                $msg = 'Acordo gerado! Cobranças criadas no Asaas.';
            } else {
                $msg = 'Acordo formalizado com sucesso! Configure o token Asaas nas configurações para gerar cobranças automáticas.';
            }

            return redirect()->route('tenant.acordos.show', $acordo->id)->with('success', $msg);
        });
    }

    /**
     * Reenvia a mensagem de WhatsApp do acordo.
     */
    public function reenviarWhatsApp(Acordo $acordo)
    {
        $devedor  = $acordo->devedor()->with('cliente')->first();
        $isPix    = $acordo->forma_pagamento === 'PIX';
        $pixChave = $isPix ? ($acordo->asaas_link ?? null) : null;
        $link     = $isPix ? null : $acordo->asaas_link;

        $enviado = $this->enviarWhatsAppAcordo($devedor, $acordo, $link, $pixChave);

        $res = $enviado
            ? 'Mensagem WhatsApp reenviada com sucesso!'
            : 'Não foi possível enviar. Verifique se o token Viício está configurado e o devedor possui telefone.';

        return back()->with($enviado ? 'success' : 'error', $res);
    }

    /**
     * Monta e dispara a mensagem de WhatsApp do acordo via Viício.
     */
    private function enviarWhatsAppAcordo(Devedor $devedor, Acordo $acordo, ?string $asaasLink, ?string $pixChave): bool
    {
        if (!$devedor->telefone) {
            return false;
        }

        $viicioToken = Setting::where('tenant_id', auth()->user()->tenant_id)
            ->where('key', 'viicio_token')
            ->value('value');

        if (!$viicioToken) {
            return false;
        }

        $valorAcordo  = 'R$ ' . number_format($acordo->valor_acordo, 2, ',', '.');
        $numParcelas  = $acordo->parcelas;
        $valorParcela = 'R$ ' . number_format(
            ($acordo->valor_acordo - ($acordo->entrada ?? 0)) / max(1, $numParcelas),
            2,
            ',',
            '.'
        );

        $msg  = "Olá {$devedor->nome}!\n\n";
        $msg .= "Seu acordo #{$acordo->id} foi firmado com sucesso!\n";
        $msg .= "Valor total: {$valorAcordo}\n";
        if (($acordo->entrada ?? 0) > 0) {
            $msg .= 'Entrada: R$ ' . number_format($acordo->entrada, 2, ',', '.') . "\n";
        }
        $msg .= "Parcelamento: {$numParcelas}x de {$valorParcela}\n\n";

        if ($acordo->forma_pagamento === 'PIX') {
            $chave = $pixChave ?: '(chave não configurada)';
            $msg .= "Pagamento via PIX\nChave PIX: {$chave}";
        } elseif ($acordo->forma_pagamento === 'CREDIT_CARD') {
            $msg .= 'Acesse o link abaixo para pagar com cartão de crédito:';
            if ($asaasLink) {
                $msg .= "\n{$asaasLink}";
            }
        } elseif ($acordo->forma_pagamento === 'BOLETO') {
            $msg .= 'Acesse o link abaixo para pagar seus boletos:';
            if ($asaasLink) {
                $msg .= "\n{$asaasLink}";
            }
        } else {
            if ($asaasLink) {
                $msg .= "Link de pagamento:\n{$asaasLink}";
            }
        }

        try {
            $whatsapp = new WhatsAppService($viicioToken);
            return (bool) $whatsapp->sendMessage($devedor->telefone, $msg);
        } catch (\Exception $e) {
            Log::warning('Falha ao enviar WhatsApp acordo #' . $acordo->id . ': ' . $e->getMessage());
            return false;
        }
    }

    public function show(Acordo $acordo)
    {
        $acordo->load(['devedor', 'acordoParcelas', 'pagamentos', 'titulos']);
        return view('tenant.acordos.show', compact('acordo'));
    }

    public function reabrir(Acordo $acordo)
    {
        if ($acordo->status === 'quitado') {
            return back()->with('error', 'Acordos quitados nao podem ser reabertos.');
        }

        DB::transaction(function () use ($acordo) {
            // Reverte títulos vinculados para em aberto
            Titulo::where('acordo_id', $acordo->id)
                ->update(['status' => 'aberto', 'acordo_id' => null]);

            // Cancela parcelas pendentes
            AcordoParcela::where('acordo_id', $acordo->id)
                ->where('status', '!=', 'pago')
                ->update(['status' => 'cancelado']);

            // Marca acordo como cancelado
            $acordo->update(['status' => 'cancelado']);
        });

        return redirect()
            ->route('tenant.devedores.show', $acordo->devedor_id)
            ->with('success', 'Acordo cancelado. Os titulos voltaram para "em aberto".');
    }
}
