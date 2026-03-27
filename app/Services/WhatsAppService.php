<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $baseUrl;

    /**
     * @param string|null $token - Se nulo, usa o Token Master do Sistema
     */
    public function __construct($token = null)
    {
        $this->baseUrl = config('services.viicio.base_url', 'https://api.viicio.com.br');
        $this->token = $token ?? config('services.viicio.master_token');
    }

    /**
     * Dispara uma mensagem de texto via Viicio API (Padrão Oficial).
     */
    public function sendMessage($phone, $message)
    {
        // Limpa o número (apenas números, mantendo DDI)
        $phoneClean = preg_replace('/[^0-9]/', '', $phone);

        if (!$this->token) {
            Log::warning("WhatsApp não disparado para {$phone}: Token VIICIO não configurado no .env.");
            return false;
        }

        try {
            // Padrão Viicio: POST /api/messages/send
            // Payload: number, body
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/messages/send", [
                'number' => $phoneClean,
                'body' => $message,
            ]);

            $responseData = $response->json();

            if ($response->successful() && ($responseData['success'] ?? false)) {
                return true;
            }

            Log::error("Falha na API Viicio: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Erro técnico ao disparar WhatsApp (Viicio): " . $e->getMessage());
            return false;
        }
    }
}
