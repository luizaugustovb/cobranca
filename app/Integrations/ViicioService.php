<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViicioService
{
    private string $baseUrl;
    private string $apiToken;
    private string $instance;

    public function __construct(string $apiToken = null, string $instance = null)
    {
        $this->apiToken = $apiToken ?? env('VIICIO_API_TOKEN');
        $this->instance = $instance ?? env('VIICIO_INSTANCE');
        $this->baseUrl = 'https://api.viicio.com.br/v1'; // Endpoint ficticio conforme solicitado
    }

    /**
     * Enviar mensagem simples via WhatsApp
     */
    public function sendMessage(string $number, string $message)
    {
        $response = Http::withToken($this->apiToken)
            ->post($this->baseUrl . '/instance/' . $this->instance . '/message/text', [
                'number' => $number,
                'message' => $message,
            ]);

        return $response->json();
    }

    /**
     * Consulta CPF/CNPJ de devedor
     */
    public function checkCpf(string $cpf)
    {
        $response = Http::withToken($this->apiToken)
            ->get($this->baseUrl . '/tools/cpf/' . $cpf);

        return $response->json();
    }

    /**
     * Enviar link de redefinição de senha
     */
    public function sendResetPassword(string $number, string $token, string $url)
    {
        $message = "Olá! Você solicitou a redefinição de sua senha na Plataforma de Cobrança.\n\n";
        $message .= "Clique no link abaixo para criar uma nova senha:\n";
        $message .= $url . "?token=" . $token . "&phone=" . $number . "\n\n";
        $message .= "Se você não solicitou isso, desconsidere esta mensagem.";

        return $this->sendMessage($number, $message);
    }
}
