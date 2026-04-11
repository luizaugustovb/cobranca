<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
                <div class="p-2 bg-emerald-100 rounded-lg mr-3 shrink-0">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                Fluxo de Caixa
            </h2>
            <a href="{{ route('tenant.relatorios') }}" class="inline-flex items-center text-xs font-black text-slate-400 hover:text-slate-700 uppercase tracking-widest transition shrink-0">
                ← Voltar aos Relatórios
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Cards de Resumo --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Recebido</p>
                    <p class="text-xl sm:text-3xl font-black text-emerald-600 tracking-tighter">R$ {{ number_format($stats['total_recebido'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Recebido este mês</p>
                    <p class="text-xl sm:text-3xl font-black text-blue-600 tracking-tighter">R$ {{ number_format($stats['recebido_mes'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Em Aberto ({{ $stats['titulos_abertos'] }} títulos)</p>
                    <p class="text-xl sm:text-3xl font-black text-amber-600 tracking-tighter">R$ {{ number_format($stats['valor_aberto'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-rose-50 rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl border border-rose-100">
                    <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">Vencidos ({{ $stats['titulos_vencidos'] }} títulos)</p>
                    <p class="text-xl sm:text-3xl font-black text-rose-600 tracking-tighter">R$ {{ number_format($stats['valor_vencido'], 2, ',', '.') }}</p>
                </div>
            </div>

            {{-- Gráfico de Recebimentos --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-6 sm:px-10 py-5 sm:py-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm sm:text-base">Recebimentos dos Últimos 12 Meses</h3>
                </div>
                <div class="p-5 sm:p-8">
                    @php $maxValor = $meses->max('valor') ?: 1; @endphp
                    <div class="flex items-end gap-1 sm:gap-2 h-40 sm:h-56">
                        @foreach($meses as $m)
                            @php $pct = ($m['valor'] / $maxValor) * 100; @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                                <span class="text-[9px] font-black text-emerald-600 truncate w-full text-center">
                                    @if($m['valor'] > 0) R${{ number_format($m['valor']/1000, 1) }}k @endif
                                </span>
                                <div class="w-full bg-emerald-500 rounded-t-lg transition-all hover:bg-emerald-600" style="height: {{ max($pct, 2) }}%"></div>
                                <span class="text-[8px] sm:text-[9px] text-slate-400 font-bold truncate w-full text-center">{{ $m['mes'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Previsão Próximos 3 Meses --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-6 sm:px-10 py-5 sm:py-8 border-b border-slate-50 bg-slate-50/30 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm sm:text-base">Previsão de Recebimento (Próximos 3 Meses)</h3>
                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Baseado em títulos em aberto</span>
                </div>
                <div class="p-5 sm:p-8">
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($previsao as $p)
                        <div class="bg-amber-50 rounded-2xl p-5 sm:p-6 text-center border border-amber-100">
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">{{ $p['mes'] }}</p>
                            <p class="text-lg sm:text-2xl font-black text-amber-700 tracking-tighter">R$ {{ number_format($p['valor'], 2, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Pagamentos Recentes --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-6 sm:px-10 py-5 sm:py-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm sm:text-base">Pagamentos Recentes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-50">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Devedor</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400 hidden sm:table-cell">Forma</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Valor</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($pagamentosRecentes as $pag)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 text-sm font-bold text-slate-800">
                                    {{ $pag->acordo?->devedor?->nome ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 hidden sm:table-cell">
                                    {{ $pag->forma_pagamento ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-black text-emerald-600 text-right">
                                    R$ {{ number_format($pag->valor, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 text-right">
                                    {{ \Carbon\Carbon::parse($pag->data_pagamento)->translatedFormat('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">Nenhum pagamento registrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
