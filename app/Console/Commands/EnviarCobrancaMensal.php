<?php

namespace App\Console\Commands;

use App\Models\Devedor;
use App\Models\Setting;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarCobrancaMensal extends Command
{
    protected $signature   = 'cobranca:disparo-mensal
        {--tenant= : ID de um tenant específico (opcional)}
        {--force : Ignora a verificação do dia do mês configurado}
        {--dry-run : Simula o disparo sem enviar nenhuma mensagem}';
    protected $description = 'Envia WhatsApp de cobrança mensal para todos os devedores com títulos em aberto';

    public function handle(): int
    {
        $tenantId = $this->option('tenant');
        $force    = $this->option('force');
        $dryRun   = $this->option('dry-run');

        $tenants = Tenant::query()
            ->where('whatsapp_ativo', true)
            ->when($tenantId, fn($q) => $q->where('id', $tenantId))
            ->get();

        $totalEnviados = 0;
        $totalErros    = 0;

        foreach ($tenants as $tenant) {
            // Verifica se o disparo mensal está habilitado para este tenant
            $settings = Setting::where('tenant_id', $tenant->id)
                ->whereIn('key', ['disparo_mensal_ativo', 'disparo_mensal_dia', 'viicio_token', 'whatsapp_mensal_texto'])
                ->pluck('value', 'key');

            if (empty($settings['disparo_mensal_ativo']) || $settings['disparo_mensal_ativo'] !== '1') {
                continue;
            }

            // Verifica se hoje é o dia configurado (a menos que --force)
            $diaCofigurado = (int) ($settings['disparo_mensal_dia'] ?? 1);
            if (!$force && Carbon::today()->day !== $diaCofigurado) {
                $this->line("Tenant '{$tenant->name}': hoje é dia " . Carbon::today()->day . ", configurado para dia {$diaCofigurado}. Pulando (use --force para ignorar).");
                continue;
            }

            $token = $settings['viicio_token'] ?? null;
            if (!$token) {
                $this->warn("Tenant #{$tenant->id} ({$tenant->name}): token Viicio não configurado, pulando.");
                continue;
            }

            $templateTexto = $settings['whatsapp_mensal_texto']
                ?? 'Olá {nome}, identificamos débito(s) em seu cadastro. Entre em contato conosco para regularizar sua situação e negociar as condições de pagamento.';

            $service  = new WhatsAppService($token);
            $devedores = Devedor::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereNotNull('telefone')
                ->whereHas('titulos', fn($q) => $q->where('status', 'aberto'))
                ->get();

            foreach ($devedores as $devedor) {
                try {
                    $tel = preg_replace('/[^0-9]/', '', $devedor->telefone ?? '');
                    if (strlen($tel) < 10) {
                        continue;
                    }

                    $primeiroNome = explode(' ', $devedor->nome)[0];
                    $qtd = $devedor->titulos()->where('status', 'aberto')->count();
                    $mensagem = str_replace(
                        ['{nome}', '{qtd}'],
                        [$primeiroNome, $qtd],
                        $templateTexto
                    );

                    $ok = $dryRun ? true : $service->sendMessage('55' . $tel, $mensagem);

                    if ($dryRun) {
                        $this->line("  [DRY-RUN] Devedor: {$devedor->nome} | Tel: {$tel} | Msg: {$mensagem}");
                    }

                    if ($ok) {
                        $totalEnviados++;
                    } else {
                        $totalErros++;
                        Log::warning("Cobrança mensal: falha ao enviar para devedor #{$devedor->id} (tenant #{$tenant->id})");
                    }
                } catch (\Throwable $e) {
                    $totalErros++;
                    Log::error("Cobrança mensal: exceção para devedor #{$devedor->id}: " . $e->getMessage());
                }
            }

            $this->info("Tenant '{$tenant->name}': {$devedores->count()} devedor(es) processado(s).");
        }

        $this->info("Disparo mensal concluído" . ($dryRun ? ' [DRY-RUN — nenhuma mensagem enviada]' : '') . ": {$totalEnviados} enviado(s), {$totalErros} erro(s).");

        return self::SUCCESS;
    }
}
