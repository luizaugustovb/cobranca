<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
                    <div class="p-2 bg-emerald-100 rounded-lg mr-3 shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    CENTRAL DE COBRANÇA
                </h2>
                <a href="{{ route('tenant.titulos.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-900 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-black transition shadow-lg text-xs self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Gerar Título
                </a>
            </div>
            {{-- Tabs de status com contadores --}}
            <div class="flex flex-wrap bg-gray-100 dark:bg-gray-800 p-1 rounded-xl gap-0.5 w-full sm:w-auto">
                @php
                $tabs = [
                'aberto' => ['label' => 'Abertos', 'activeClass' => 'text-amber-600'],
                'negociado' => ['label' => 'Negociados', 'activeClass' => 'text-blue-600'],
                'pago' => ['label' => 'Pagos', 'activeClass' => 'text-emerald-600'],
                'cancelado' => ['label' => 'Cancelados', 'activeClass' => 'text-red-500'],
                ];
                @endphp
                @foreach($tabs as $tabStatus => $tab)
                <a href="{{ route('tenant.titulos', ['status' => $tabStatus]) }}"
                    class="relative flex-1 sm:flex-none px-3 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition text-center
                           {{ $status === $tabStatus
                               ? 'bg-white dark:bg-gray-700 shadow ' . $tab['activeClass']
                               : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
                    {{ $tab['label'] }}
                    @if(isset($counts[$tabStatus]) && $counts[$tabStatus] > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full text-[9px] font-black
                                {{ $status === $tabStatus ? 'bg-current/10' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}
                                px-1">{{ $counts[$tabStatus] }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    {{-- Modal de confirmação de cancelamento (Alpine.js) --}}
    <div x-data="{
            showModal: false,
            tituloId: null,
            tituloNumero: '',
            confirmar(id, numero) {
                this.tituloId = id;
                this.tituloNumero = numero;
                this.showModal = true;
            }
        }">

        {{-- Overlay do modal --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="showModal = false">
            <div @click.stop
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-5 sm:p-8 max-w-md w-full mx-4 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-red-100 shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 dark:text-white text-lg tracking-tight">Cancelar Título</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Esta ação não pode ser desfeita facilmente.</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Você está prestes a cancelar o título <strong x-text="'#' + tituloNumero" class="text-slate-800 dark:text-white"></strong>.
                    O título não será deletado, mas passará para o status <strong>Cancelado</strong> e sairá da cobrança ativa.
                </p>
                <div class="flex gap-3">
                    <button @click="showModal = false"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm uppercase tracking-widest transition">
                        Voltar
                    </button>
                    {{-- Form de cancelamento que usa o tituloId dinamicamente --}}
                    <form :action="'/tenant/titulos/' + tituloId + '/cancelar'" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-black text-sm uppercase tracking-widest transition shadow-lg shadow-red-500/30">
                            Confirmar Cancelamento
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="font-semibold">{{ session('error') }}</p>
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Nº Título</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Devedor</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Vencimento</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Valor Original</th>
                                    <th class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Encargos</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($titulos as $titulo)
                                @php
                                $rowBg = match($titulo->status) {
                                'negociado' => 'bg-blue-50/40 dark:bg-blue-900/10',
                                'pago' => 'bg-emerald-50/30 dark:bg-emerald-900/10',
                                'cancelado' => 'bg-gray-50/60 dark:bg-gray-700/20',
                                default => '',
                                };
                                @endphp
                                <tr class="{{ $rowBg }} hover:opacity-90 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-tighter">#{{ $titulo->numero }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('tenant.devedores.show', $titulo->devedor_id) }}"
                                            class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-tighter hover:text-blue-600 transition">
                                            {{ $titulo->devedor->nome }}
                                        </a>
                                        <div class="text-[10px] text-gray-400 font-bold tracking-widest">{{ $titulo->devedor->cpf_cnpj }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm {{ $titulo->vencimento->isPast() && $titulo->status === 'aberto' ? 'text-red-500 font-black' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ $titulo->vencimento->format('d/m/Y') }}
                                            @if($titulo->vencimento->isPast() && $titulo->status === 'aberto')
                                            <span class="ml-1.5 text-[9px] bg-red-100 text-red-600 font-black uppercase px-1.5 py-0.5 rounded-full">Vencido</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base font-black text-slate-900 dark:text-white">R$ {{ number_format($titulo->valor_original, 2, ',', '.') }}</div>
                                        @if($titulo->status === 'aberto' || $titulo->status === 'negociado')
                                        <div class="text-xs text-gray-400 font-medium">Total: R$ {{ number_format($titulo->valor_total, 2, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        @if(($titulo->juros + $titulo->multa + $titulo->honorarios) > 0)
                                        <div class="text-xs text-red-400 font-medium">J+M: R$ {{ number_format(($titulo->juros + $titulo->multa), 2, ',', '.') }}</div>
                                        @if($titulo->honorarios > 0)
                                        <div class="text-xs text-amber-500 font-medium">Hon: R$ {{ number_format($titulo->honorarios, 2, ',', '.') }}</div>
                                        @endif
                                        @else
                                        <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                        $badgeClass = match($titulo->status) {
                                        'aberto' => 'bg-amber-100 text-amber-700',
                                        'negociado' => 'bg-blue-100 text-blue-700',
                                        'pago' => 'bg-emerald-100 text-emerald-700',
                                        'cancelado' => 'bg-red-100 text-red-600',
                                        default => 'bg-gray-100 text-gray-600',
                                        };
                                        $badgeLabel = match($titulo->status) {
                                        'aberto' => 'Aberto',
                                        'negociado' => 'Negociado',
                                        'pago' => 'Pago',
                                        'cancelado' => 'Cancelado',
                                        default => ucfirst($titulo->status),
                                        };
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-black uppercase rounded-full tracking-wide {{ $badgeClass }}">
                                            {{ $badgeLabel }}
                                        </span>
                                        @if($titulo->status === 'negociado' && $titulo->acordo)
                                        <div class="mt-1">
                                            <a href="{{ route('tenant.acordos.show', $titulo->acordo_id) }}"
                                                class="text-[10px] text-blue-500 hover:text-blue-700 font-black uppercase tracking-widest underline">
                                                Ver Acordo #{{ $titulo->acordo_id }}
                                            </a>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            {{-- Ir para devedor --}}
                                            <a href="{{ route('tenant.devedores.show', $titulo->devedor_id) }}"
                                                title="Ver Devedor"
                                                class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-500 hover:text-white transition shadow-sm">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </a>

                                            @if($titulo->status === 'negociado')
                                            {{-- Negociado: só mostra link para o acordo --}}
                                            <a href="{{ route('tenant.acordos.show', $titulo->acordo_id) }}"
                                                title="Ver Acordo"
                                                class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition shadow-sm">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                            @elseif($titulo->status === 'aberto')
                                            {{-- Aberto: WhatsApp + editar + cancelar --}}
                                            @php
                                            $telTitulo = preg_replace('/[^0-9]/', '', $titulo->devedor->telefone ?? '');
                                            $telWaTitulo = strlen($telTitulo) >= 10 ? '55' . $telTitulo : '';
                                            $templateMsg = $settings['whatsapp_cobranca_texto'] ?? 'Ola {nome}, consta em nosso sistema o titulo #{numero} no valor de R$ {valor} com vencimento em {vencimento}. Entre em contato para regularizacao.';
                                            $msgTitulo = str_replace(
                                            ['{nome}', '{numero}', '{valor}', '{vencimento}'],
                                            [
                                            explode(' ', $titulo->devedor->nome)[0],
                                            $titulo->numero,
                                            number_format($titulo->valor_total, 2, ',', '.'),
                                            $titulo->vencimento->format('d/m/Y'),
                                            ],
                                            $templateMsg
                                            );
                                            @endphp
                                            @if($telWaTitulo)
                                            <button
                                                x-data="{ st: 'idle' }"
                                                @click="if(st!='idle')return;st='loading';fetch('{{ route('tenant.whatsapp.disparar') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({phone:'{{ $telWaTitulo }}',message:@js($msgTitulo)})}).then(r=>r.json()).then(d=>{st=d.success?'sent':'idle';if(!d.success)alert(d.error||'Erro ao enviar.');}).catch(()=>{st='idle';alert('Erro de conexão.');})"
                                                :disabled="st==='loading'"
                                                :title="st==='sent'?'Mensagem enviada!':'Enviar cobrança via WhatsApp'"
                                                :class="st==='sent'?'p-2 bg-green-500 text-white rounded-lg':'p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-500 hover:text-white transition shadow-sm'">
                                                <svg x-show="st!=='loading'&&st!=='sent'" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.171.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.512-2.961-2.628-.086-.117-.704-.933-.704-1.782 0-.85.433-1.268.587-1.442.155-.174.337-.217.45-.217l.323.004c.103.005.23.02.361.33.136.323.466 1.137.507 1.219.04.083.067.18.013.287-.054.107-.081.174-.162.27-.081.094-.17.21-.242.282-.081.082-.166.171-.072.332.094.162.418.689.897 1.115.617.551 1.137.721 1.3.8.163.078.261.066.359-.045.099-.112.424-.492.537-.66.113-.168.225-.141.38-.084.155.057.986.465 1.155.549.169.085.281.127.322.197.041.07.041.405-.103.81z" />
                                                </svg>
                                                <svg x-show="st==='loading'" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                                <svg x-show="st==='sent'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            @endif
                                            <a href="{{ route('tenant.titulos.edit', $titulo) }}"
                                                title="Editar"
                                                class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-500 hover:text-white transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button @click="confirmar({{ $titulo->id }}, '{{ addslashes($titulo->numero) }}')"
                                                title="Cancelar Título"
                                                class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            @else
                                            {{-- Pago / Cancelado: só editar --}}
                                            <a href="{{ route('tenant.titulos.edit', $titulo) }}"
                                                title="Editar"
                                                class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-500 hover:text-white transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-10 h-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm font-bold uppercase tracking-widest">Nenhum título com este status.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                        {{ $titulos->appends(['status' => $status])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>