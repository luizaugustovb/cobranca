<x-app-layout>
    <x-slot name="header">
         <div class="flex flex-col">
            <h2 class="font-black text-2xl sm:text-4xl text-slate-800 leading-none uppercase tracking-tighter">
                Receitas do SaaS
            </h2>
            <div class="flex items-center mt-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Faturamento Recorrente (Tenants)</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto">
            
            <!-- Grid de Stats Financeiros (Novo Estilo) -->
            <div class="grid grid-cols-3 gap-6 mb-16">
                <!-- Recebido -->
                <div class="bg-emerald-500 rounded-[2.5rem] p-6 sm:p-10 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <p class="text-[10px] font-black text-white/50 uppercase tracking-[0.2em] mb-2">Faturamento Recebido</p>
                    <p class="text-2xl sm:text-4xl font-black text-white tracking-tighter">R$ {{ number_format($totais['recebido'], 2, ',', '.') }}</p>
                </div>
                
                <!-- Pendente -->
                <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-slate-100 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">A Receber (Mês)</p>
                        <p class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tighter">R$ {{ number_format($totais['pendente'], 2, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Inadimplência -->
                <div class="bg-rose-50 rounded-[2.5rem] p-6 sm:p-10 shadow-xl border border-rose-100 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.2em] mb-2">Inadimplência Tenants</p>
                        <p class="text-2xl sm:text-4xl font-black text-rose-900 tracking-tighter">R$ {{ number_format($totais['vencido'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabela Refinada -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="px-6 sm:px-10 py-6 sm:py-8 border-b border-slate-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 bg-slate-50/20">
                    <h3 class="font-black text-lg text-slate-800 uppercase tracking-tighter">Histórico de Cobranças (Assinaturas)</h3>
                    <div class="px-4 py-2 bg-white rounded-xl border border-slate-100 text-[10px] font-black uppercase text-blue-500 shadow-sm flex items-center">
                        <svg class="w-3 h-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Gestão via Asaas API
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-50">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-10 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Escritório Cliente</th>
                                <th class="px-10 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Vencimento</th>
                                <th class="px-10 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Valor</th>
                                <th class="px-10 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                                <th class="px-10 py-5 text-right text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Controle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse ($cobrancas as $cobranca)
                                <tr class="hover:bg-slate-50 transition duration-300">
                                    <td class="px-10 py-7">
                                        <div class="font-black text-slate-800 uppercase text-xs tracking-tighter">{{ $cobranca->tenant->name }}</div>
                                        <div class="text-[9px] text-slate-400 font-bold tracking-[0.2em] uppercase mt-1">CNPJ: {{ $cobranca->tenant->document }}</div>
                                    </td>
                                    <td class="px-10 py-7 text-xs font-bold text-slate-500 uppercase">{{ $cobranca->vencimento->format('d/m/Y') }}</td>
                                    <td class="px-10 py-7 font-black text-slate-900 text-sm">R$ {{ number_format($cobranca->valor, 2, ',', '.') }}</td>
                                    <td class="px-10 py-7">
                                        <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $cobranca->status === 'pago' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $cobranca->status }}
                                        </span>
                                    </td>
                                    <td class="px-10 py-7 text-right">
                                        <button class="text-[10px] font-black text-blue-500 hover:text-blue-800 uppercase tracking-widest bg-blue-50 px-4 py-2 rounded-xl transition">Acessar no Asaas</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-10 py-24 text-center">
                                         <p class="text-xs font-black text-slate-300 uppercase tracking-[0.3em]">Aguardando faturamento automático</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
