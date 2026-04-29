<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Troca de senha obrigatória (primeiro acesso)
    Route::get('/password/force-change', [\App\Http\Controllers\Auth\ForcePasswordChangeController::class, 'show'])->name('password.force-change');
    Route::post('/password/force-change', [\App\Http\Controllers\Auth\ForcePasswordChangeController::class, 'update'])->name('password.force-change.update');

    // Tenant Routes (Módulos Principais)
    Route::prefix('tenant')->name('tenant.')->group(function () {
        // Clientes CRUD
        Route::get('/clientes', [\App\Http\Controllers\Tenant\ClienteController::class, 'index'])->name('clientes');
        Route::get('/clientes/create', [\App\Http\Controllers\Tenant\ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [\App\Http\Controllers\Tenant\ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}/relatorio', [\App\Http\Controllers\Tenant\ClienteController::class, 'relatorio'])->name('clientes.relatorio');
        Route::get('/clientes/{cliente}/relatorio/pdf', [\App\Http\Controllers\Tenant\ClienteController::class, 'relatorioPdf'])->name('clientes.relatorio.pdf');
        Route::get('/clientes/{cliente}/edit', [\App\Http\Controllers\Tenant\ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [\App\Http\Controllers\Tenant\ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [\App\Http\Controllers\Tenant\ClienteController::class, 'destroy'])->name('clientes.destroy');
        // Devedores CRUD
        Route::get('/devedores', [\App\Http\Controllers\Tenant\DevedorController::class, 'index'])->name('devedores');
        Route::get('/devedores/create', [\App\Http\Controllers\Tenant\DevedorController::class, 'create'])->name('devedores.create');
        Route::post('/devedores', [\App\Http\Controllers\Tenant\DevedorController::class, 'store'])->name('devedores.store');
        Route::get('/devedores/{devedor}', [\App\Http\Controllers\Tenant\DevedorController::class, 'show'])->name('devedores.show');
        Route::get('/devedores/{devedor}/edit', [\App\Http\Controllers\Tenant\DevedorController::class, 'edit'])->name('devedores.edit');
        Route::put('/devedores/{devedor}', [\App\Http\Controllers\Tenant\DevedorController::class, 'update'])->name('devedores.update');
        Route::delete('/devedores/{devedor}', [\App\Http\Controllers\Tenant\DevedorController::class, 'destroy'])->name('devedores.destroy');
        // Títulos (Central de Cobrança)
        Route::get('/titulos', [\App\Http\Controllers\Tenant\TituloController::class, 'index'])->name('titulos');
        Route::get('/titulos/create', [\App\Http\Controllers\Tenant\TituloController::class, 'create'])->name('titulos.create');
        Route::post('/titulos', [\App\Http\Controllers\Tenant\TituloController::class, 'store'])->name('titulos.store');
        Route::get('/titulos/{titulo}/edit', [\App\Http\Controllers\Tenant\TituloController::class, 'edit'])->name('titulos.edit');
        Route::put('/titulos/{titulo}', [\App\Http\Controllers\Tenant\TituloController::class, 'update'])->name('titulos.update');
        Route::post('/titulos/{titulo}/cancelar', [\App\Http\Controllers\Tenant\TituloController::class, 'cancel'])->name('titulos.cancel');
        // Recalcular juros/multa/IPCA dos títulos em aberto de um devedor
        Route::post('/devedores/{devedor}/recalcular-titulos', [\App\Http\Controllers\Tenant\RecalcularTitulosController::class, 'recalcular'])->name('titulos.recalcular');
        // Negociações (Acordos)
        Route::get('/acordos', [\App\Http\Controllers\Tenant\AcordoController::class, 'index'])->name('acordos');
        Route::get('/acordos/create', [\App\Http\Controllers\Tenant\AcordoController::class, 'create'])->name('acordos.create');
        Route::post('/acordos', [\App\Http\Controllers\Tenant\AcordoController::class, 'store'])->name('acordos.store');
        Route::get('/acordos/{acordo}', [\App\Http\Controllers\Tenant\AcordoController::class, 'show'])->name('acordos.show');
        Route::post('/acordos/{acordo}/reabrir', [\App\Http\Controllers\Tenant\AcordoController::class, 'reabrir'])->name('acordos.reabrir');
        Route::post('/acordos/{acordo}/whatsapp', [\App\Http\Controllers\Tenant\AcordoController::class, 'reenviarWhatsApp'])->name('acordos.whatsapp');
        // Pagamentos (Módulo Financeiro)
        Route::get('/pagamentos', [\App\Http\Controllers\Tenant\PagamentoController::class, 'index'])->name('pagamentos');
        Route::get('/pagamentos/create', [\App\Http\Controllers\Tenant\PagamentoController::class, 'create'])->name('pagamentos.create');
        Route::post('/pagamentos', [\App\Http\Controllers\Tenant\PagamentoController::class, 'store'])->name('pagamentos.store');
        // Conversor PDF → XLSX / Leitura direta
        Route::get('/pdf-conversao', [\App\Http\Controllers\Tenant\PdfConversaoController::class, 'create'])->name('pdf-conversao.create');
        Route::post('/pdf-conversao', [\App\Http\Controllers\Tenant\PdfConversaoController::class, 'store'])->name('pdf-conversao.store');
        Route::post('/pdf-conversao/preview', [\App\Http\Controllers\Tenant\PdfConversaoController::class, 'preview'])->name('pdf-conversao.preview');
        Route::post('/pdf-conversao/importar', [\App\Http\Controllers\Tenant\PdfConversaoController::class, 'importar'])->name('pdf-conversao.importar');

        // Importações (Lotes)
        Route::get('/importacoes', [\App\Http\Controllers\Tenant\ImportacaoController::class, 'index'])->name('importacoes');
        Route::get('/importacoes/template', [\App\Http\Controllers\Tenant\ImportacaoController::class, 'template'])->name('importacoes.template');
        Route::get('/importacoes/create', [\App\Http\Controllers\Tenant\ImportacaoController::class, 'create'])->name('importacoes.create');
        Route::post('/importacoes', [\App\Http\Controllers\Tenant\ImportacaoController::class, 'store'])->name('importacoes.store');
        Route::get('/importacoes/{importacao}', [\App\Http\Controllers\Tenant\ImportacaoController::class, 'show'])->name('importacoes.show');
        Route::get('/importacoes/{importacao}/download', [\App\Http\Controllers\Tenant\ImportacaoController::class, 'download'])->name('importacoes.download');

        // Importação Activesoft PDF
        Route::get('/importacoes/activesoft/upload', [\App\Http\Controllers\Tenant\ImportacaoActivesoftController::class, 'create'])->name('importacoes.activesoft');
        Route::post('/importacoes/activesoft/preview', [\App\Http\Controllers\Tenant\ImportacaoActivesoftController::class, 'preview'])->name('importacoes.activesoft.preview');
        Route::post('/importacoes/activesoft/confirmar', [\App\Http\Controllers\Tenant\ImportacaoActivesoftController::class, 'confirmar'])->name('importacoes.activesoft.confirmar');

        Route::get('/relatorios', [\App\Http\Controllers\Tenant\RelatorioController::class, 'index'])->name('relatorios');
        Route::get('/relatorios/fluxo-caixa', [\App\Http\Controllers\Tenant\RelatorioController::class, 'fluxoCaixa'])->name('relatorios.fluxo-caixa');
        Route::get('/relatorios/eficiencia', [\App\Http\Controllers\Tenant\RelatorioController::class, 'eficiencia'])->name('relatorios.eficiencia');
        Route::get('/relatorios/auditoria', [\App\Http\Controllers\Tenant\RelatorioController::class, 'auditoria'])->name('relatorios.auditoria');

        // WhatsApp (Viicio API)
        Route::post('/whatsapp/disparar', [\App\Http\Controllers\Tenant\WhatsAppController::class, 'disparar'])->name('whatsapp.disparar');

        // Configurações
        Route::get('/configuracoes', [\App\Http\Controllers\Tenant\SettingController::class, 'index'])->name('settings');
        Route::post('/configuracoes', [\App\Http\Controllers\Tenant\SettingController::class, 'store'])->name('settings.store');

        // Usuários do Escritório
        Route::get('/usuarios', [\App\Http\Controllers\Tenant\UserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/create', [\App\Http\Controllers\Tenant\UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [\App\Http\Controllers\Tenant\UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{user}/edit', [\App\Http\Controllers\Tenant\UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [\App\Http\Controllers\Tenant\UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [\App\Http\Controllers\Tenant\UserController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Admin Geral Routes (Painel do SaaS)
    Route::middleware('role:Admin Geral')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'index'])->name('tenants');
        Route::get('/tenants/create', [\App\Http\Controllers\Admin\TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}/edit', [\App\Http\Controllers\Admin\TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'destroy'])->name('tenants.destroy');
        Route::post('/tenants/{tenant}/reset-password', [\App\Http\Controllers\Admin\TenantController::class, 'resetPassword'])->name('tenants.reset-password');
        Route::get('/tenants/{tenant}/impersonate', [\App\Http\Controllers\Admin\ImpersonationController::class, 'start'])->name('impersonate');
        Route::post('/tenants/stop-impersonation', [\App\Http\Controllers\Admin\ImpersonationController::class, 'stop'])->name('stop-impersonation');

        // Financeiro Global do SaaS (Receitas de Assinaturas)
        Route::get('/financeiro', [\App\Http\Controllers\Admin\FinanceiroController::class, 'index'])->name('financeiro');

        // Configurações Globais SaaS
        Route::get('/configuracoes', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
        Route::post('/configuracoes', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
        Route::post('/configuracoes/teste-whatsapp', [\App\Http\Controllers\Admin\SettingController::class, 'testWhatsApp'])->name('settings.test-whatsapp');

        // Planos & Preços
        Route::get('/planos', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('planos');
        Route::put('/planos', [\App\Http\Controllers\Admin\PlanController::class, 'update'])->name('planos.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
