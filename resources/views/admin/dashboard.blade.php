<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-black text-4xl text-slate-800 leading-none uppercase tracking-tighter">
                Painel Global
            </h2>
            <div class="flex items-center mt-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">SaaS Health: Operacional</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Grid de Estatísticas (Unificado Premium) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                
                <!-- Card 1 -->
                <div class="bg-white rounded-[2rem] p-10 shadow-xl border border-slate-100 flex flex-col justify-between hover:shadow-2xl transition-all duration-500 group">
                    <div>
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total de Tenants</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['total_tenants'] }}</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 flex items-center text-[9px] font-black text-emerald-500 uppercase tracking-widest">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path></svg>
                        Plataforma Ativa
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-[2rem] p-10 shadow-xl border border-slate-100 flex flex-col justify-between hover:shadow-2xl transition-all duration-500 group">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-500">
                             <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Escritórios ativos</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['active_tenants'] }}</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 flex items-center text-[9px] font-black text-blue-500 uppercase tracking-widest">
                        Sincronização Ativa
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-[2rem] p-10 shadow-xl border border-slate-100 flex flex-col justify-between hover:shadow-2xl transition-all duration-500 group">
                    <div>
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-600 mb-6 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Usuários globais</p>
                        <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 flex items-center text-[9px] font-black text-slate-500 uppercase tracking-widest">
                        Total de Contas
                    </div>
                </div>

                <!-- Card 4 (Destaque Financeiro Soft) -->
                <div class="bg-slate-900 rounded-[2rem] p-10 shadow-2xl shadow-indigo-900/20 flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    <div>
                        <div class="w-12 h-12 bg-indigo-600/20 rounded-2xl flex items-center justify-center text-indigo-400 mb-6">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Receita Processada</p>
                        <p class="text-3xl font-black text-white tracking-tighter leading-none">R$ {{ number_format($stats['global_revenue'], 2, ',', '.') }}</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-800 flex items-center text-[9px] font-black text-indigo-400 uppercase tracking-widest">
                        MÉTRICA DE PERFORMANCE
                    </div>
                </div>

            </div>

            <!-- Audit Logs Global (Refinado) -->
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-10 py-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <h3 class="font-black text-lg text-slate-800 tracking-tighter uppercase mb-0 leading-none">Atividades Recentes - Global</h3>
                    <div class="flex items-center space-x-2 bg-white px-4 py-2 rounded-full border border-slate-100 shadow-sm">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Monitor Automático Ativo</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-50">
                        <tbody class="bg-white divide-y divide-slate-50">
                            @forelse($recentTenants as $t)
                            <tr class="hover:bg-slate-50/50 transition cursor-default">
                                <td class="px-10 py-6">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black mr-6 uppercase shadow-sm text-xs">
                                            {{ strtoupper(substr($t->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-800 uppercase tracking-tighter">{{ $t->name }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold tracking-tight">{{ $t->email }} &mdash; Plano {{ strtoupper($t->plan) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <span @class([
                                        'inline-block px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest',
                                        'bg-emerald-100 text-emerald-700' => $t->status === 'active',
                                        'bg-amber-100 text-amber-700' => $t->status === 'inactive',
                                        'bg-red-100 text-red-700' => $t->status === 'suspended',
                                    ])>{{ $t->status }}</span>
                                    <p class="text-[9px] text-slate-300 font-black uppercase tracking-widest mt-1">{{ $t->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-10 py-12 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">
                                    Nenhum escritório cadastrado ainda.
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
