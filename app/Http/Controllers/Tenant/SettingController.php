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
            'asaas_token' => 'nullable|string',
            'viicio_token' => 'nullable|string',
            'viicio_instance' => 'nullable|string',
            'company_name' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
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

        return redirect()->route('tenant.settings')->with('success', 'Configurações atualizadas com sucesso!');
    }
}
