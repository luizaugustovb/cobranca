<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('tenant.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asaas_token'               => 'nullable|string',
            'viicio_token'              => 'nullable|string',
            'company_name'              => 'nullable|string',
            'logo'                      => 'nullable|image|max:2048',
            'honorarios_tipo'           => 'nullable|in:fixo,percentual',
            'honorarios_valor'               => 'nullable|numeric|min:0',
            'whatsapp_cobranca_texto'       => 'nullable|string|max:1000',
            'whatsapp_autoatendimento_texto' => 'nullable|string|max:1000',
            'disparo_mensal_ativo'          => 'nullable|in:0,1',
            'disparo_mensal_dia'            => 'nullable|integer|min:1|max:28',
            'whatsapp_mensal_texto'         => 'nullable|string|max:1000',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'logo' && $request->hasFile('logo')) {
                $value = $request->file('logo')->store('logos', 'public');
            }

            Setting::updateOrCreate(
                ['key' => $key, 'tenant_id' => auth()->user()->tenant_id],
                ['value' => $value]
            );
        }

        // Garante que disparo_mensal_ativo seja salvo mesmo quando checkbox desmarcado (não vem no POST)
        if (!$request->has('disparo_mensal_ativo')) {
            Setting::updateOrCreate(
                ['key' => 'disparo_mensal_ativo', 'tenant_id' => auth()->user()->tenant_id],
                ['value' => '0']
            );
        }

        return redirect()->route('tenant.settings')->with('success', 'Configurações atualizadas com sucesso!');
    }
}
