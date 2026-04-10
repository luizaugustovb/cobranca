<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Integrations\AsaasService;
use App\Models\Acordo;
use App\Models\AcordoParcela;
use App\Models\Devedor;
use App\Models\Setting;
use App\Models\Titulo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcordoController extends Controller
{
    public function index()
    {
        $acordos = Acordo::with('devedor')->orderBy('created_at', 'desc')->paginate(10);
        return view('tenant.acordos.index', compact('acordos'));
    }

    public function create(Request $request)
    {
        $devedorId = $request->get('devedor');
        $devedor = Devedor::with(['titulos' => function($q) {
            $q->where('status', 'aberto');
        }])->findOrFail($devedorId);

        $totalPrincipal  = $devedor->titulos->sum('valor_original');
        $totalJuros      = $devedor->titulos->sum('juros');
        $totalMulta      = $devedor->titulos->sum('multa');
        $totalHonorarios = $devedor->titulos->sum('honorarios');
        $totalOriginal   = $totalPrincipal + $totalJuros + $totalMulta + $totalHonorarios;
        
        return view('tenant.acordos.create', compact(
            'devedor', 'totalOriginal', 'totalPrincipal', 'totalJuros', 'totalMulta', 'totalHonorarios'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'devedor_id'         => 'required|exists:devedores,id',
            'valor_original'     => 'required|numeric',
            'desconto'           => 'required|numeric|min:0',
            'valor_acordo'       => 'required|numeric',
            'entrada'            => 'required|numeric|min:0',
            'parcelas'           => 'required|integer|min:1|max:48',
            'vencimento_primeira'=> 'required|date|after_or_equal:today',
            'forma_pagamento'    => 'required|string|in:BOLETO,PIX,CREDIT_CARD,UNDEFINED',
        ]);

        return DB::transaction(function () use ($request) {
            $devedor = Devedor::findOrFail($request->devedor_id);

            // --- Integração Asaas ---
            $asaasToken      = Setting::where('key', 'asaas_token')->value('value');
            $asaasCustomerId = $devedor->asaas_customer_id;
            $asaasLink       = null;

            if ($asaasToken) {
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

            // 2. Marcar títulos como negociados (vinculados ao acordo — não cancelados)
            Titulo::where('devedor_id', $devedor->id)
                ->where('status', 'aberto')
                ->update(['status' => 'negociado', 'acordo_id' => $acordo->id]);

            // 3. Gerar Parcelas + criar cobranças no Asaas
            $valorParcela   = round(($request->valor_acordo - $request->entrada) / $request->parcelas, 2);
            $dataVencimento = Carbon::parse($request->vencimento_primeira);

            for ($i = 1; $i <= $request->parcelas; $i++) {
                $paymentId = null;

                if ($asaasToken && $asaasCustomerId) {
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

            if ($asaasLink) {
                $acordo->update(['asaas_link' => $asaasLink]);
            }

            $msg = $asaasToken && $asaasCustomerId
                ? 'Acordo gerado! Cobranças criadas no Asaas — o devedor pode pagar via boleto, PIX ou cartão.'
                : 'Acordo formalizado com sucesso! Configure o token Asaas nas configurações para gerar cobranças automáticas.';

            return redirect()->route('tenant.devedores.show', $devedor->id)->with('success', $msg);
        });
    }

    public function show(Acordo $acordo)
    {
        $acordo->load(['devedor', 'acordoParcelas', 'pagamentos', 'titulos']);
        return view('tenant.acordos.show', compact('acordo'));
    }
}
