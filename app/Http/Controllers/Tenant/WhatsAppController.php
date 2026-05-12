<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function disparar(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        $tenantId = auth()->user()->tenant_id;

        // Verifica se o WhatsApp está habilitado para este escritório
        $tenant = Tenant::find($tenantId);
        if (!$tenant || !$tenant->whatsapp_ativo) {
            return response()->json([
                'success' => false,
                'error'   => 'Envio de WhatsApp desativado para este escritório. Contate o administrador.',
            ], 403);
        }

        $tenantToken = Setting::where('tenant_id', $tenantId)
            ->where('key', 'viicio_token')
            ->value('value');

        if (!$tenantToken) {
            return response()->json([
                'success' => false,
                'error'   => 'Token Viicio não configurado. Acesse Configurações → Notificações WhatsApp.',
            ], 422);
        }

        $service = new WhatsAppService($tenantToken);
        $ok = $service->sendMessage($request->phone, $request->message);

        if ($ok) {
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'error'   => 'Falha ao enviar via API Viicio. Verifique o token nas configurações.',
        ], 500);
    }
}
