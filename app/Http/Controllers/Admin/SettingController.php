<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        // Lê diretamente do .env para não depender do cache
        $settings = [
            'viicio_master_token'   => $this->readEnv('VIICIO_MASTER_TOKEN'),
            'viicio_base_url'       => $this->readEnv('VIICIO_BASE_URL', 'https://api.viicio.com.br'),
            'asas_master_token'     => $this->readEnv('ASAS_MASTER_TOKEN'),
            'asas_mode'             => $this->readEnv('ASAS_MODE', 'sandbox'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'viicio_master_token' => 'nullable|string',
            'viicio_base_url'     => 'nullable|string',
            'asas_master_token'   => 'nullable|string',
            'asas_mode'           => 'nullable|string|in:sandbox,production',
        ]);

        $this->updateEnv('VIICIO_MASTER_TOKEN', $validated['viicio_master_token'] ?? '');
        $this->updateEnv('VIICIO_BASE_URL', $validated['viicio_base_url'] ?? 'https://api.viicio.com.br');
        $this->updateEnv('ASAS_MASTER_TOKEN', $validated['asas_master_token'] ?? '');
        $this->updateEnv('ASAS_MODE', $validated['asas_mode'] ?? 'sandbox');

        // Regenera o config cache para refletir os novos valores
        Artisan::call('config:cache');

        return redirect()->back()->with('success', 'Configurações de integração atualizadas e salvas com sucesso no servidor!');
    }

    private function readEnv(string $key, string $default = ''): string
    {
        $path = base_path('.env');
        if (!file_exists($path)) return $default;

        $content = file_get_contents($path);
        if (preg_match("/^{$key}=(.*)$/m", $content, $matches)) {
            $value = trim($matches[1]);
            return trim($value, '"\'');
        }
        return $default;
    }

    private function updateEnv(string $key, string $value): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) return;

        // Envolve em aspas se contiver espaços
        $stored = (str_contains($value, ' ')) ? '"' . $value . '"' : $value;

        $content = file_get_contents($path);

        if (str_contains($content, "{$key}=")) {
            $content = preg_replace("/^{$key}=.*$/m", "{$key}={$stored}", $content);
        } else {
            $content .= PHP_EOL . "{$key}={$stored}";
        }

        file_put_contents($path, $content);
    }

    public function testWhatsApp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $service = new WhatsAppService();

        $message = "🔹 *TESTE DE CONEXÃO - COBRANÇAPRO* 🔹\n\nOlá! Este é um disparo de teste automático do seu sistema SaaS.\n\n✅ *Status:* Operacional\n✅ *Token:* Configurado\n\nData: " . now()->format('d/m/Y H:i');

        if ($service->sendMessage($phone, $message)) {
            return redirect()->back()->with('success', '🚀 Mensagem de teste enviada com sucesso para ' . $phone . '!');
        }

        return redirect()->back()->with('error', '❌ Falha ao disparar WhatsApp. Verifique seu Token Master nas configurações.');
    }
}
