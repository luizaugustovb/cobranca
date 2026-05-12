<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <nav class="flex mb-4 text-xs font-bold text-gray-400 uppercase tracking-widest" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('tenant.devedores') }}" class="hover:text-blue-600">Devedores</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Detalhes</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-black text-2xl sm:text-4xl text-slate-900 dark:text-white flex items-center tracking-tighter uppercase">
                    {{ $devedor->nome }}
                </h2>
                <div class="mt-2 flex items-center space-x-4">
                    <span class="bg-blue-600 text-white text-xs px-3 py-1 rounded-full font-bold tracking-widest">{{ $devedor->cpf_cnpj }}</span>
                    <span class="text-gray-400 font-medium text-sm">Cliente: <strong class="text-gray-700">{{ $devedor->cliente->nome }}</strong></span>
                </div>
            </div>
            <div class="flex space-x-3">
                @php
                $telDevedor = preg_replace('/[^0-9]/', '', $devedor->telefone ?? '');
                $telWaDevedor = strlen($telDevedor) >= 10 ? '55' . $telDevedor : '';
                $titulosAbertos = $devedor->titulos->where('status', 'aberto');
                $totalAberto = $titulosAbertos->sum(fn($t) => $t->valor_total);
                $qtdAbertos = $titulosAbertos->count();
                $primeiroNome = explode(' ', $devedor->nome)[0];
                $settingsWa = \App\Models\Setting::all()->pluck('value', 'key');
                $templateAuto = $settingsWa['whatsapp_autoatendimento_texto'] ?? 'Olá {nome}, identificamos débito(s) em seu cadastro em nosso sistema. Entre em contato conosco para regularizar sua situação e negociar as condições de pagamento.';
                $msgCobranca = str_replace(
                ['{nome}', '{qtd}', '{total}'],
                [$primeiroNome, $qtdAbertos, number_format($totalAberto, 2, ',', '.')],
                $templateAuto
                );
                @endphp
                @if($telWaDevedor)
                <button
                    x-data="{ st: 'idle' }"
                    @click="if(st!='idle')return;st='loading';fetch('{{ route('tenant.whatsapp.disparar') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({phone:'{{ $telWaDevedor }}',message:@js($msgCobranca)})}).then(r=>r.json()).then(d=>{st=d.success?'sent':'idle';if(!d.success)alert(d.error||'Erro ao enviar.');}).catch(()=>{st='idle';alert('Erro de conexão.');})"
                    :disabled="st==='loading'"
                    :class="st==='sent'?'inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl font-bold text-sm uppercase shadow-lg shadow-green-500/20':'inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-xl font-bold text-sm uppercase transition hover:bg-green-600 shadow-lg shadow-green-500/20'">
                    <svg x-show="st!=='loading'&&st!=='sent'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.171.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.512-2.961-2.628-.086-.117-.704-.933-.704-1.782 0-.85.433-1.268.587-1.442.155-.174.337-.217.45-.217l.323.004c.103.005.23.02.361.33.136.323.466 1.137.507 1.219.04.083.067.18.013.287-.054.107-.081.174-.162.27-.081.094-.17.21-.242.282-.081.082-.166.171-.072.332.094.162.418.689.897 1.115.617.551 1.137.721 1.3.8.163.078.261.066.359-.045.099-.112.424-.492.537-.66.113-.168.225-.141.38-.084.155.057.986.465 1.155.549.169.085.281.127.322.197.041.07.041.405-.103.81z" />
                    </svg>
                    <svg x-show="st==='loading'" class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg x-show="st==='sent'" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span x-text="st==='sent'?'Enviado!':'WhatsApp'"></span>
                </button>
                @else
                <span class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-400 rounded-xl font-bold text-sm uppercase cursor-not-allowed" title="Telefone nao cadastrado">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.171.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.512-2.961-2.628-.086-.117-.704-.933-.704-1.782 0-.85.433-1.268.587-1.442.155-.174.337-.217.45-.217l.323.004c.103.005.23.02.361.33.136.323.466 1.137.507 1.219.04.083.067.18.013.287-.054.107-.081.174-.162.27-.081.094-.17.21-.242.282-.081.082-.166.171-.072.332.094.162.418.689.897 1.115.617.551 1.137.721 1.3.8.163.078.261.066.359-.045.099-.112.424-.492.537-.66.113-.168.225-.141.38-.084.155.057.986.465 1.155.549.169.085.281.127.322.197.041.07.041.405-.103.81z" />
                    </svg>
                    WhatsApp
                </span>
                @endif
                <form method="POST" action="{{ route('tenant.titulos.recalcular', $devedor) }}"
                    onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm uppercase transition shadow-sm"
                        title="Recalcula juros, multa, IPCA e honorários dos títulos em aberto com base nas taxas do cliente">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Recalcular Dívidas
                    </button>
                </form>
                <a href="{{ route('tenant.devedores.edit', $devedor) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm uppercase transition hover:bg-gray-50 shadow-sm">
                    Editar Perfil
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-6 py-4 shadow">
                <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-6 py-4 shadow">
                <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-bold">{{ session('error') }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Coluna da Esquerda: Resumo e Títulos -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Card de Dívidas -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="bg-blue-600 px-8 py-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <h3 class="text-white font-black text-xl tracking-tighter flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                DÍVIDAS ATIVAS
                            </h3>
                            <div class="text-white text-right">
                                <p class="text-xs uppercase font-thin leading-tight">Total em Aberto</p>
                                <p class="text-2xl font-black">R$ {{ number_format($devedor->titulos->where('status', 'aberto')->sum(fn($t) => $t->valor_total), 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="p-0 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/30">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Nº Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Vencimento</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Valor Atualizado</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">
                                    @forelse($devedor->titulos as $titulo)
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Nenhum título vinculado.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @foreach($devedor->titulos as $titulo)
                                @php $det = $titulo->detalhamentoCorrigido; @endphp
                                <tbody x-data="{ open: false }"
                                    class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer bg-white dark:bg-gray-800"
                                        @click="open = !open">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700 dark:text-white uppercase">
                                            <span class="mr-1 text-gray-300 text-xs" x-text="open ? '▲' : '▼'"></span>
                                            {{ $titulo->numero }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $titulo->vencimento->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-black text-slate-800 dark:text-white">R$ {{ number_format($titulo->valor_total, 2, ',', '.') }}</span>
                                            @if(round($titulo->valor_total, 2) != round($det['valor_original'], 2))
                                            <span class="ml-1 text-xs text-gray-400 line-through">R$ {{ number_format($det['valor_original'], 2, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="px-3 py-1 text-xs font-black uppercase rounded-full {{ $titulo->status === 'aberto' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                {{ $titulo->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- Linha de detalhamento expansível --}}
                                    <tr x-show="open" class="bg-amber-50 dark:bg-amber-900/10">
                                        <td colspan="4" class="px-8 py-4">
                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-center">
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Original</p>
                                                    <p class="text-sm font-black text-slate-700">R$ {{ number_format($det['valor_original'], 2, ',', '.') }}</p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Multa ({{ $det['multa_percentual'] }}%)</p>
                                                    <p class="text-sm font-black text-orange-600">+ R$ {{ number_format($det['multa'], 2, ',', '.') }}</p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Juros ({{ $det['juros_mensal'] }}% × {{ $det['meses_atraso'] }}m)</p>
                                                    <p class="text-sm font-black text-red-600">+ R$ {{ number_format($det['juros_acumulado'], 2, ',', '.') }}</p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">IPCA ({{ number_format($det['ipca_acumulado'], 2, ',', '.') }}% acum.)</p>
                                                    <p class="text-sm font-black text-yellow-600">+ R$ {{ number_format($det['correcao_ipca'], 2, ',', '.') }}</p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Honorários ({{ $det['hon_percentual'] }}%)</p>
                                                    <p class="text-sm font-black text-purple-600">+ R$ {{ number_format($det['honorarios'], 2, ',', '.') }}</p>
                                                </div>
                                                <div class="bg-blue-600 rounded-xl p-3 shadow-sm">
                                                    <p class="text-[10px] font-bold text-blue-100 uppercase mb-1">Total Corrigido</p>
                                                    <p class="text-sm font-black text-white">R$ {{ number_format($det['total'], 2, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            @if($titulo->status === 'aberto' && round($det['total'], 2) != round($titulo->valor_total, 2))
                                            <p class="mt-3 text-xs text-amber-700 font-bold text-center">
                                                ⚠ Valor desatualizado. Clique em <strong>Recalcular Dívidas</strong> no topo para gravar os valores corrigidos.
                                            </p>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                                @endforeach
                            </table>
                        </div>
                        @if($devedor->titulos->where('status', 'aberto')->count() > 0)
                        <div class="px-8 py-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('tenant.acordos.create', ['devedor' => $devedor->id]) }}" class="w-full inline-flex justify-center items-center px-6 py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest transition hover:bg-blue-700 shadow-xl shadow-blue-500/30">
                                INICIAR NEGOCIAÇÃO
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Acordos Recentes -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <h3 class="font-black text-xl text-slate-800 dark:text-white tracking-tighter uppercase">Acordos / Negociações</h3>
                            <svg class="h-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="p-8 space-y-4">
                            @forelse($devedor->acordos as $acordo)
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center border border-gray-100 dark:border-gray-600">
                                <div class="mb-4 md:mb-0">
                                    <p class="text-xs font-bold text-gray-400 uppercase uppercase mb-1">Status: <span class="text-blue-600">{{ $acordo->status }}</span></p>
                                    <p class="text-2xl font-black text-slate-800 dark:text-white tracking-widest">R$ {{ number_format($acordo->valor_acordo, 2, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 font-medium">Em {{ $acordo->parcelas }}x parcelas</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('tenant.acordos.show', $acordo) }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-500 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-xs uppercase hover:bg-gray-50 transition shadow-sm">Ver Detalhes</a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <p class="text-gray-400 italic">Nenhuma negociação formalizada recentemente.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Coluna da Direita: Dados de Contato e Histórico -->
                <div class="space-y-8">

                    <!-- Dados de Contato -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-5 sm:p-8 border border-gray-100 dark:border-gray-700 transition hover:shadow-2xl">
                        <h3 class="font-black text-lg text-slate-800 dark:text-white tracking-tighter uppercase mb-6 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Contato Direto
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">WhatsApp / Telefone</p>
                                <p class="text-lg font-bold text-slate-700 dark:text-white tracking-wide">{{ $devedor->telefone ?? 'Não informado' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">E-mail Principal</p>
                                <p class="text-sm font-bold text-blue-600 break-all">{{ $devedor->email ?? 'Não informado' }}</p>
                            </div>
                            <hr class="border-gray-50 dark:border-gray-700">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Último Contato</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Há 3 dias (Não atendeu)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Histórico de Interações -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700"
                        x-data="{ aberto: false }">
                        <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <h3 class="font-black text-lg text-slate-800 dark:text-white tracking-tighter uppercase">LINHA DO TEMPO</h3>
                            <button type="button"
                                @click="aberto = !aberto"
                                :class="aberto ? 'p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition' : 'p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition'"
                                title="Adicionar registro">
                                <svg x-show="!aberto" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <svg x-show="aberto" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Formulário de novo registro --}}
                        <div x-show="aberto" x-transition class="border-b border-gray-100 dark:border-gray-700 bg-blue-50/60 dark:bg-gray-700/40 p-6">
                            <form action="{{ route('tenant.devedores.contatos.store', $devedor) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Tipo de contato</label>
                                        <select name="tipo" required
                                            class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-400 bg-white dark:bg-gray-700 text-sm font-medium py-2.5">
                                            <option value="">Selecione...</option>
                                            <option value="Ligação">Ligação</option>
                                            <option value="WhatsApp">WhatsApp</option>
                                            <option value="E-mail">E-mail</option>
                                            <option value="Visita">Visita</option>
                                            <option value="Tentativa de Acordo">Tentativa de Acordo</option>
                                            <option value="Acordo Fechado">Acordo Fechado</option>
                                            <option value="Promessa de Pagamento">Promessa de Pagamento</option>
                                            <option value="Não Atendeu">Não Atendeu</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Resultado (opcional)</label>
                                        <input type="text" name="resultado" maxlength="255"
                                            class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-400 bg-white dark:bg-gray-700 text-sm font-medium py-2.5"
                                            placeholder="Ex: Não atendeu, promessou pagar dia 05..." />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Descrição / Observações</label>
                                    <textarea name="descricao" required rows="3" maxlength="2000"
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-400 bg-white dark:bg-gray-700 text-sm font-medium"
                                        placeholder="Descreva o que foi tratado, o resultado da tentativa de contato..."></textarea>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="aberto = false"
                                        class="px-4 py-2 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition">Cancelar</button>
                                    <button type="submit"
                                        class="px-5 py-2 rounded-xl bg-blue-600 text-white text-sm font-black uppercase tracking-wide hover:bg-blue-700 transition shadow">
                                        Salvar Registro
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="p-8 overflow-y-auto max-h-[400px]">
                            <ul class="space-y-6">
                                @forelse($devedor->contatos->sortByDesc('created_at') as $contato)
                                <li class="relative pl-6 border-l-2 border-blue-100">
                                    <div class="absolute -left-1.5 top-0 w-3 h-3 rounded-full bg-blue-500 border-2 border-white"></div>
                                    <div class="text-xs font-bold text-blue-600 uppercase mb-0.5">{{ $contato->tipo }}</div>
                                    <div class="text-[10px] text-gray-400 font-semibold mb-1">
                                        {{ \Carbon\Carbon::parse($contato->created_at)->format('d/m/Y \à\s H:i') }}
                                        @if($contato->resultado)
                                        &mdash; <span class="text-amber-500">{{ $contato->resultado }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $contato->descricao }}</p>
                                </li>
                                @empty
                                <p class="text-center text-gray-400 italic text-sm">Sem histórico registrado.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>