<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
                <div class="p-2 bg-blue-100 rounded-lg mr-3 shrink-0">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                EficiÃªncia Operacional
            </h2>
            <a href="{{ route('tenant.relatorios') }}" class="inline-flex items-center text-xs font-black text-slate-400 hover:text-slate-700 uppercase tracking-widest transition shrink-0">
                â† Voltar aos RelatÃ³rios
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Cards de Taxas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl text-white">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Taxa de RecuperaÃ§Ã£o</p>
                    <p class="text-4xl sm:text-5xl font-black tracking-tighter">{{ $taxaRecuperacao }}%</p>
                    <p class="text-xs font-medium mt-2 opacity-70">TÃ­tulos pagos + em acordo / total</p>
                </div>
                <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ConversÃ£o em Acordos</p>
                    <p class="text-4xl sm:text-5xl font-black text-blue-600 tracking-tighter">{{ $taxaConversao }}%</p>
                    <p class="text-xs font-medium text-slate-400 mt-2">{{ $titulosAcordo }} de {{ $totalTitulos }} tÃ­tulos negociados</p>
                </div>
                <div class="bg-rose-50 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl border border-rose-100">
                    <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">InadimplÃªncia Residual</p>
                    <p class="text-4xl sm:text-5xl font-black text-rose-600 tracking-tighter">{{ $taxaInadimplencia }}%</p>
                    <p class="text-xs font-medium text-rose-400 mt-2">{{ $titulosAbertos }} tÃ­tulos em aberto</p>
                </div>
            </div>

            {{-- Resumo de TÃ­tulos e Acordos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- TÃ­tulos --}}
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="px-6 sm:px-8 py-5 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm">TÃ­tulos â€” VisÃ£o Geral</h3>
                    </div>
                    <div class="p-6 sm:p-8 space-y-4">
                        @php
                            $barras = [
                                ['label' => 'Pagos', 'qtd' => $titulosPagos, 'cor' => 'bg-emerald-500'],
                                ['label' => 'Em Acordo', 'qtd' => $titulosAcordo, 'cor' => 'bg-blue-500'],
                                ['label' => 'Em Aberto', 'qtd' => $titulosAbertos, 'cor' => 'bg-amber-400'],
                                ['label' => 'Cancelados', 'qtd' => $titulosCancelados, 'cor' => 'bg-slate-300'],
                            ];
                            $maxQtd = collect($barras)->max('qtd') ?: 1;
                        @endphp
                        @foreach($barras as $b)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-black text-slate-600 uppercase tracking-wider">{{ $b['label'] }}</span>
                                <span class="text-xs font-black text-slate-500">{{ $b['qtd'] }}</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="{{ $b['cor'] }} h-full rounded-full transition-all" style="width: {{ ($b['qtd'] / $maxQtd) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="pt-4 border-t border-slate-50 flex justify-between">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total</span>
                            <span class="text-xs font-black text-slate-700">{{ $totalTitulos }}</span>
                        </div>
                    </div>
                </div>

                {{-- Acordos --}}
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="px-6 sm:px-8 py-5 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm">Acordos â€” VisÃ£o Geral</h3>
                    </div>
                    <div class="p-6 sm:p-8 space-y-4">
                        @php
                            $acordoBarras = [
                                ['label' => 'Ativos', 'qtd' => $acordosAtivos, 'cor' => 'bg-blue-500'],
                                ['label' => 'ConcluÃ­dos', 'qtd' => $acordosConcluidos, 'cor' => 'bg-emerald-500'],
                                ['label' => 'Inadimplentes', 'qtd' => $acordosInad, 'cor' => 'bg-rose-400'],
                            ];
                            $maxAcordo = collect($acordoBarras)->max('qtd') ?: 1;
                        @endphp
                        @foreach($acordoBarras as $b)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-black text-slate-600 uppercase tracking-wider">{{ $b['label'] }}</span>
                                <span class="text-xs font-black text-slate-500">{{ $b['qtd'] }}</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="{{ $b['cor'] }} h-full rounded-full transition-all" style="width: {{ ($b['qtd'] / $maxAcordo) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="pt-4 border-t border-slate-50 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Valor Original Negociado</span>
                                <span class="text-xs font-black text-slate-700">R$ {{ number_format($valorOriginal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Valor Final dos Acordos</span>
                                <span class="text-xs font-black text-emerald-600">R$ {{ number_format($valorNegociado, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Desconto MÃ©dio Concedido</span>
                                <span class="text-xs font-black text-blue-600">{{ $descontoMedio }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acordos por MÃªs --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-6 sm:px-10 py-5 sm:py-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm sm:text-base">Acordos Criados â€” Ãšltimos 6 Meses</h3>
                </div>
                <div class="p-5 sm:p-8">
                    @php $maxAcordoMes = $acordosPorMes->max('qtd') ?: 1; @endphp
                    <div class="flex items-end gap-2 sm:gap-4 h-32 sm:h-48">
                        @foreach($acordosPorMes as $a)
                        @php $pct = ($a['qtd'] / $maxAcordoMes) * 100; @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                            <span class="text-[10px] font-black text-blue-600">{{ $a['qtd'] > 0 ? $a['qtd'] : '' }}</span>
                            <div class="w-full bg-blue-500 rounded-t-lg hover:bg-blue-600 transition-all" style="height: {{ max($pct, 2) }}%"></div>
                            <span class="text-[9px] text-slate-400 font-bold truncate w-full text-center">{{ $a['mes'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Ãšltimos Acordos --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-6 sm:px-10 py-5 sm:py-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm sm:text-base">Ãšltimos Acordos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-50">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Devedor</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-slate-400 hidden sm:table-cell">Valor Original</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Valor Acordo</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-slate-400 hidden md:table-cell">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($ultimosAcordos as $acordo)
                            @php
                                $cores = ['ativo'=>'bg-blue-100 text-blue-700','concluido'=>'bg-emerald-100 text-emerald-700','inadimplente'=>'bg-rose-100 text-rose-700'];
                                $cor = $cores[$acordo->status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ $acordo->devedor?->nome ?? 'â€”' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500 text-right hidden sm:table-cell">R$ {{ number_format($acordo->valor_original, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-black text-blue-600 text-right">R$ {{ number_format($acordo->valor_acordo, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $cor }}">{{ ucfirst($acordo->status) }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 text-right hidden md:table-cell">
                                    {{ $acordo->created_at->translatedFormat('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">Nenhum acordo encontrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
