<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-lg text-slate-800 uppercase tracking-tighter">
                {{ __('Visão Geral') }}
            </h2>
            <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                {{ $tenantName ?? 'Operação Global' }} <span class="mx-1 text-slate-300">|</span> <span class="text-indigo-600">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Grid de Indicadores Principais -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- Card 1: Receita -->
                <div class="relative bg-white rounded-2xl shadow-md border border-emerald-100 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-400 rounded-t-2xl"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-emerald-50 p-2.5 rounded-xl">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100">Mês Atual</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Recebido</p>
                        <p class="text-3xl font-black text-slate-900 tracking-tighter leading-none">R$ {{ number_format($totais['pagamentos_mes'] ?? 0, 2, ',', '.') }}</p>
                        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 mr-2"></div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight">Fluxo em dia</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Títulos -->
                <div class="relative bg-white rounded-2xl shadow-md border border-amber-100 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-amber-400 rounded-t-2xl"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-amber-50 p-2.5 rounded-xl">
                                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            @if(($totais['titulos_abertos'] ?? 0) > 0)
                                <span class="text-[9px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2 py-1 rounded-full border border-amber-100">Atenção</span>
                            @else
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-50 px-2 py-1 rounded-full border border-slate-100">Zerado</span>
                            @endif
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Títulos em Aberto</p>
                        <p class="text-3xl font-black text-slate-900 tracking-tighter leading-none">{{ $totais['titulos_abertos'] ?? 0 }}</p>
                        <div class="mt-4 pt-4 border-t border-slate-50">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total: <span class="text-slate-600">R$ {{ number_format($totais['valor_aberto'] ?? 0, 2, ',', '.') }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Acordos -->
                <div class="relative bg-white rounded-2xl shadow-md border border-indigo-100 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-500 rounded-t-2xl"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-indigo-50 p-2.5 rounded-xl">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-indigo-500 bg-indigo-50 px-2 py-1 rounded-full border border-indigo-100">Ativos</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Acordos Ativos</p>
                        <p class="text-3xl font-black text-slate-900 tracking-tighter leading-none">{{ $totais['acordos_ativos'] ?? 0 }}</p>
                        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center">
                            <div class="w-2 h-2 rounded-full bg-indigo-400 mr-2"></div>
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-tight">Em recuperação</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Devedores -->
                <div class="relative bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-slate-400 rounded-t-2xl"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-slate-100 p-2.5 rounded-xl">
                                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 bg-slate-100 px-2 py-1 rounded-full border border-slate-200">Carteira</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Base de Devedores</p>
                        <p class="text-3xl font-black text-slate-900 tracking-tighter leading-none">{{ $totais['devedores'] ?? 0 }}</p>
                        <div class="mt-4 pt-4 border-t border-slate-50">
                            <a href="{{ route('tenant.devedores') }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline underline-offset-4">Gerenciar carteira &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Seção de Relatórios Detalhados -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal (Gráficos) -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Desempenho da Recuperação</h3>
                            <p class="text-xs text-slate-400 mt-1 italic">Projeção estimada para o trimestre corrente.</p>
                        </div>
                        <button class="text-[10px] font-black text-slate-400 uppercase border border-slate-200 px-4 py-2 rounded-xl hover:bg-slate-50 transition">Exportar PDF</button>
                    </div>
                    <div class="flex flex-col items-center justify-center h-48 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-100">
                        <div class="flex space-x-2 items-end mb-4">
                            <div class="w-4 h-12 bg-indigo-200 rounded-t-sm"></div>
                            <div class="w-4 h-8 bg-indigo-100 rounded-t-sm"></div>
                            <div class="w-4 h-20 bg-indigo-400 rounded-t-sm"></div>
                            <div class="w-4 h-14 bg-indigo-300 rounded-t-sm"></div>
                            <div class="w-4 h-16 bg-indigo-500 rounded-t-sm"></div>
                        </div>
                        <p class="text-slate-400 uppercase font-black text-[9px] tracking-widest italic">Processando Inteligência de Dados...</p>
                    </div>
                </div>

                <!-- Sidebar de Ações / Histórico -->
                <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-500/20">
                    <h3 class="text-xs font-black uppercase tracking-widest mb-6 opacity-80">Ações Sugeridas</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3 p-3 bg-white/10 rounded-2xl border border-white/10 hover:bg-white/20 transition cursor-pointer">
                            <div class="bg-white/20 p-2 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-tight">Cobrança em Massa</p>
                                <p class="text-[10px] opacity-60 leading-tight mt-1">Disparar WhatsApp para {{ $totais['titulos_vencidos_hoje'] ?? 0 }} título(s) vencido(s) hoje.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-3 bg-white/10 rounded-2xl border border-white/10 hover:bg-white/20 transition cursor-pointer">
                            <div class="bg-white/20 p-2 rounded-xl">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-tight">Relatório SaaS</p>
                                <p class="text-[10px] opacity-60 leading-tight mt-1">Baixar demonstrativo de receita por escritório.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-white/10">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-2">Suporte Prioritário</p>
                        <p class="text-[10px] leading-tight italic">Link direto com consultor técnico para ajuda na operação.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
