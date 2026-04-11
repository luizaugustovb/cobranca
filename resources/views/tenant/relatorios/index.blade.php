<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            <div class="p-2 bg-purple-100 rounded-lg mr-3 shadow-lg shadow-purple-500/10 shrink-0">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            INTELIGÃŠNCIA E RELATÃ“RIOS
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- RelatÃ³rio Fluxo de Caixa -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 group">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-4 bg-emerald-50 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tighter uppercase mb-2">Fluxo de Caixa</h3>
                        <p class="text-sm text-gray-400 font-medium mb-8">Analise recebimentos mensais, previsÃµes e baixas em tempo real.</p>
                        <a href="{{ route('tenant.relatorios.fluxo-caixa') }}" class="inline-flex items-center text-emerald-600 font-black uppercase text-xs tracking-widest hover:underline">Acessar RelatÃ³rio â†’</a>
                    </div>
                </div>

                <!-- EficiÃªncia de RecuperaÃ§Ã£o -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 group">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-4 bg-blue-50 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tighter uppercase mb-2">EficiÃªncia Operacional</h3>
                        <p class="text-sm text-gray-400 font-medium mb-8">ConversÃ£o de tÃ­tulos em acordos e taxa de inadimplÃªncia residual.</p>
                        <a href="{{ route('tenant.relatorios.eficiencia') }}" class="inline-flex items-center text-blue-600 font-black uppercase text-xs tracking-widest hover:underline">Acessar RelatÃ³rio â†’</a>
                    </div>
                </div>

                 <!-- Auditoria e Logs -->
                 <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 group">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-4 bg-indigo-50 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tighter uppercase mb-2">Logs de Auditoria</h3>
                        <p class="text-sm text-gray-400 font-medium mb-8">Rastro completo de quem visualizou ou alterou cada dado no tenant.</p>
                        <a href="{{ route('tenant.relatorios.auditoria') }}" class="inline-flex items-center text-indigo-600 font-black uppercase text-xs tracking-widest hover:underline">Acessar RelatÃ³rio â†’</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
