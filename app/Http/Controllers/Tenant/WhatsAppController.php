<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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

        $tenantToken = Setting::where('tenant_id', auth()->user()->tenant_id)
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
