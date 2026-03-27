<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.asaas.token', env('ASAAS_TOKEN'));
        $this->baseUrl = config('services.asaas.env', env('ASAAS_ENV')) === 'production' 
            ? 'https://www.asaas.com/api/v3' 
            : 'https://sandbox.asaas.com/api/v3';
    }

    /**
     * Criar cliente no Asaas
     */
    public function createCustomer(array $data)
    {
        $response = Http::withHeader('access_token', $this->apiKey)
            ->post($this->baseUrl . '/customers', [
                'name' => $data['nome'],
                'cpfCnpj' => $data['documento'],
                'email' => $data['email'] ?? null,
                'mobilePhone' => $data['telefone'] ?? null,
                'notificationDisabled' => false,
            ]);

        return $response->json();
    }

    /**
     * Criar cobrança
     */
    public function createPayment(array $data)
    {
        $response = Http::withHeader('access_token', $this->apiKey)
            ->post($this->baseUrl . '/payments', [
                'customer' => $data['customer_id'],
                'billingType' => $data['type'], // BOLETO, PIX, CREDIT_CARD
                'value' => $data['value'],
                'dueDate' => $data['due_date'],
                'description' => $data['description'] ?? 'Cobrança Plataforma SaaS',
                'externalReference' => $data['reference'] ?? null,
            ]);

        return $response->json();
    }

    /**
     * Obter detalhes da cobrança
     */
    public function getPayment($id)
    {
        $response = Http::withHeader('access_token', $this->apiKey)
            ->get($this->baseUrl . '/payments/' . $id);

        return $response->json();
    }

    /**
     * Gerar link de pagamento (para cartao/pix/boleto)
     */
    public function getPaymentLink($id)
    {
        $response = Http::withHeader('access_token', $this->apiKey)
            ->get($this->baseUrl . '/payments/' . $id . '/identificationField');

        return $response->json();
    }
}
