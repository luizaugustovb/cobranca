<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        // Pega todos os tenants do sistema
        $tenants = Tenant::paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $plans = Plan::where('ativo', true)->orderBy('valor')->get();
        return view('admin.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tenants,slug|max:255',
            'document' => 'required|string|unique:tenants,document|max:255',
            'email' => 'required|email|unique:tenants,email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'status' => 'required|string|in:active,inactive,suspended',
            'plan' => 'required|string',
        ], [
            'email.unique' => 'Este e-mail já está em uso por outro escritório ou usuário. Use um e-mail diferente.',
        ]);

        $tenant = Tenant::create($validated);

        // --- INTEGRAÇÃO VIICIO: Criação automática de sub-conta (Empresa) ---
        try {
            $masterToken = config('services.viicio.master_token');
            if ($masterToken) {
                $vResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => "Bearer {$masterToken}",
                    'Content-Type' => 'application/json'
                ])->post(config('services.viicio.base_url') . '/api/companyCreate', [
                    'name' => $tenant->name,
                    'email' => $tenant->email,
                    'phone' => preg_replace('/[^0-9]/', '', $request->phone ?? ''),
                    'password' => \Illuminate\Support\Str::random(12),
                    'status' => 'true',
                    'planId' => Plan::where('slug', $tenant->plan)->value('viicio_plan_id') ?? 1,
                    'dueDate' => now()->addMonth()->format('Y-m-d'),
                    'recurrence' => 'mensal',
                    'document' => preg_replace('/[^0-9]/', '', $tenant->document),
                    'paymentMethod' => 'fatura',
                    'companyUserName' => 'Admin ' . $tenant->name
                ]);

                if ($vResponse->successful() && isset($vResponse->json()['company'])) {
                    $vData = $vResponse->json()['company'];
                    $tenant->update([
                        'viicio_token' => $vData['token'] ?? null,
                        'viicio_company_id' => $vData['id'] ?? null
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro Viicio ao criar tenant: " . $e->getMessage());
        }

        // Criar automaticamente uma cobrança inicial para o tenant
        $valorPlano = Plan::where('slug', $validated['plan'])->value('valor') ?? 0;
        \App\Models\SaasCobranca::create([
            'tenant_id' => $tenant->id,
            'valor' => $valorPlano,
            'vencimento' => now()->addDays(7),
            'status' => 'pendente',
            'asaas_id' => 'sim_'.uniqid(), // Simulando ID do Asaas para o sistema do admin
        ]);

        // Criar o usuário administrador do escritório com senha padrão
        $password = 'Admin@123';
        $userExists = \App\Models\User::withoutGlobalScopes()->where('email', $tenant->email)->exists();
        if ($userExists) {
            $tenant->delete();
            return redirect()->back()->withInput()->withErrors([
                'email' => 'Este e-mail já está em uso por outro usuário no sistema. Use um e-mail diferente.',
            ]);
        }
        $user = \App\Models\User::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin - ' . $tenant->name,
            'email' => $tenant->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'must_change_password' => true,
        ]);

        // Disparar WhatsApp com credenciais de acesso
        $whatsappEnviado = false;
        try {
            $phone = preg_replace('/[^0-9]/', '', $request->phone ?? '');
            if ($phone) {
                $link = config('app.url');
                $mensagem = "✅ *Bem-vindo ao CobrançaPro!*\n\n"
                    . "Seu escritório *{$tenant->name}* foi cadastrado com sucesso na plataforma.\n\n"
                    . "📧 *Login:* {$tenant->email}\n"
                    . "🔑 *Senha:* {$password}\n"
                    . "🔗 *Acesso:* {$link}\n\n"
                    . "⚠️ No primeiro acesso você será solicitado a criar uma senha pessoal.\n\n"
                    . "_CobrançaPro — Desenvolvido por LAVB Tecnologias_";

                $whatsappEnviado = (new \App\Services\WhatsAppService())->sendMessage($phone, $mensagem);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao enviar WhatsApp de boas-vindas: " . $e->getMessage());
        }

        $whatsappStatus = $whatsappEnviado
            ? ' ✅ WhatsApp de boas-vindas enviado ao escritório.'
            : ' ⚠️ WhatsApp NÃO enviado (verifique o Token Viicio nas Configurações).';

        return redirect()->route('admin.tenants')->with('success',
            "Escritório '{$tenant->name}' criado! Login: {$tenant->email} | Senha padrão: {$password} |{$whatsappStatus}"
        );
    }

    public function resetPassword(Tenant $tenant)
    {
        $newPassword = 'Admin@123';

        // Busca o usuário admin pelo tenant (qualquer usuário do tenant)
        $user = \App\Models\User::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')
            ->first();

        if (!$user) {
            // Fallback: busca pelo e-mail (pode estar associado a outro tenant_id por falha anterior)
            $user = \App\Models\User::withoutGlobalScopes()
                ->where('email', $tenant->email)
                ->first();

            if ($user) {
                // Corrige o tenant_id caso esteja errado
                $user->tenant_id = $tenant->id;
            } else {
                // Cria o usuário do zero
                $user = new \App\Models\User();
                $user->tenant_id = $tenant->id;
                $user->name      = 'Admin - ' . $tenant->name;
                $user->email     = $tenant->email;
            }
        }

        $user->password           = \Illuminate\Support\Facades\Hash::make($newPassword);
        $user->must_change_password = true;
        $user->save();

        $loginEmail = $user->email;

        // Disparar WhatsApp com nova senha
        $whatsappEnviado = false;
        try {
            $phone = preg_replace('/[^0-9]/', '', $tenant->phone ?? '');
            if ($phone) {
                $link = config('app.url');
                $mensagem = "🔑 *Redefinição de Senha — CobrançaPro*\n\n"
                    . "Olá! A senha da sua conta foi redefinida pelo administrador do sistema.\n\n"
                    . "📧 *Login:* {$loginEmail}\n"
                    . "🔑 *Nova Senha:* {$newPassword}\n"
                    . "🔗 *Acesso:* {$link}\n\n"
                    . "⚠️ No próximo acesso você será solicitado a criar uma nova senha pessoal.\n\n"
                    . "_CobrançaPro — Desenvolvido por LAVB Tecnologias_";

                $token = $tenant->viicio_token ?: null;
                $whatsappEnviado = (new \App\Services\WhatsAppService($token))->sendMessage($phone, $mensagem);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao enviar WhatsApp de reset: " . $e->getMessage());
        }

        $whatsappStatus = $whatsappEnviado
            ? ' ✅ WhatsApp com nova senha enviado ao escritório.'
            : ' ⚠️ WhatsApp NÃO enviado (verifique o Token Viicio).';

        return redirect()->back()->with('success',
            "Senha de '{$tenant->name}' redefinida! Login: {$loginEmail} | Senha: {$newPassword} |{$whatsappStatus}"
        );
    }

    public function edit(Tenant $tenant)
    {
        $plans = Plan::where('ativo', true)->orderBy('valor')->get();
        return view('admin.tenants.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tenants,slug,' . $tenant->id,
            'document' => 'required|string|unique:tenants,document,' . $tenant->id,
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'status' => 'required|string|in:active,inactive,suspended',
            'plan' => 'required|string',
            'viicio_token' => 'nullable|string|max:255',
        ]);

        $tenant->fill($validated);
        // viicio_token pode ser enviado em branco (limpar) ou preenchido
        $tenant->viicio_token = $request->input('viicio_token') ?: null;
        $tenant->save();

        return redirect()->route('admin.tenants')->with('success', 'Escritório atualizado com sucesso!');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->status !== 'inactive') {
            return redirect()->back()->with('error', 'Apenas escritórios inativos podem ser excluídos.');
        }

        // Remove todos os registros dependentes antes de excluir o tenant
        \DB::table('saas_cobrancas')->where('tenant_id', $tenant->id)->delete();
        \DB::table('audit_logs')->where('tenant_id', $tenant->id)->delete();
        \DB::table('settings')->where('tenant_id', $tenant->id)->delete();
        \DB::table('status_cobranca')->where('tenant_id', $tenant->id)->delete();
        \DB::table('historico_contatos')->where('tenant_id', $tenant->id)->delete();
        \DB::table('anexos')->where('tenant_id', $tenant->id)->delete();
        \DB::table('pagamentos')->where('tenant_id', $tenant->id)->delete();
        \DB::table('acordo_parcelas')->where('tenant_id', $tenant->id)->delete();
        \DB::table('acordos')->where('tenant_id', $tenant->id)->delete();
        \DB::table('importacoes')->where('tenant_id', $tenant->id)->delete();
        \DB::table('titulos')->where('tenant_id', $tenant->id)->delete();
        \DB::table('devedores')->where('tenant_id', $tenant->id)->delete();
        \DB::table('clientes')->where('tenant_id', $tenant->id)->delete();
        \DB::table('alunos')->where('tenant_id', $tenant->id)->delete();
        \DB::table('users')->where('tenant_id', $tenant->id)->delete();
        \DB::table('tenant_integrations')->where('tenant_id', $tenant->id)->delete();
        \DB::table('api_request_logs')->where('tenant_id', $tenant->id)->delete();
        \DB::table('webhook_logs')->where('tenant_id', $tenant->id)->delete();

        $tenant->delete();

        return redirect()->route('admin.tenants')->with('success', 'Escritório e todos os seus dados foram removidos permanentemente.');
    }
}
