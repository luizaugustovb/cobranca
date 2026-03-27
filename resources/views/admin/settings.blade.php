<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-black text-2xl text-slate-900 leading-none uppercase tracking-tighter">
                Configurações da Plataforma
            </h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2 px-1">Gestão de Infraestrutura e Planos SaaS</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <form action="{{ route('admin.settings.test-whatsapp') }}" method="POST" id="test-whatsapp-form" class="hidden">
                @csrf
                <input type="hidden" name="phone" id="hidden_test_phone">
            </form>

            <form action="{{ route('admin.settings.store') }}" method="POST">
                @csrf
                
                    <!-- Seção: Pagamentos e Mensagens -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                        <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-indigo-50/20">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Infraestrutura: Pagamentos & WhatsApp</h3>
                            </div>
                        </div>
                        
                        <div class="p-8 space-y-8">
                            <!-- Bloco: Viicio -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4 items-end">
                                <div class="space-y-2">
                                    <x-input-label for="viicio_master_token" value="Token Master Viicio" class="text-[10px] font-black uppercase text-slate-400" />
                                    <x-text-input id="viicio_master_token" name="viicio_master_token" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="$settings['viicio_master_token']" placeholder="Insira o seu Token Bearer" />
                                </div>
                                <div class="space-y-2">
                                    <x-input-label for="viicio_base_url" value="API Endpoint URL" class="text-[10px] font-black uppercase text-slate-400" />
                                    <x-text-input id="viicio_base_url" name="viicio_base_url" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="$settings['viicio_base_url']" />
                                </div>
                                <div class="space-y-2 flex items-center space-x-3">
                                    <div class="flex-grow">
                                        <x-input-label for="test_phone" value="📲 Celular para Teste" class="text-[10px] font-black uppercase text-slate-400" />
                                        <x-text-input id="test_phone" name="test_phone" type="text" class="w-full bg-white border border-indigo-100 rounded-2xl py-4" placeholder="55849..." />
                                    </div>
                                    <button type="submit" form="test-whatsapp-form" class="mt-6 px-6 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition shadow-lg shadow-emerald-500/20">
                                        Testar
                                    </button>
                                </div>
                            </div>

                            <div class="h-px bg-slate-50"></div>

                            <!-- Bloco: Asas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4">
                                <div class="space-y-2">
                                    <x-input-label for="asas_master_token" value="Token Master do ASAS (API Key)" class="text-[10px] font-black uppercase text-slate-400" />
                                    <x-text-input id="asas_master_token" name="asas_master_token" type="text" class="w-full bg-white border border-indigo-100 rounded-2xl py-4" :value="$settings['asas_master_token']" placeholder="$aask_..." />
                                    <p class="text-[9px] text-indigo-400 font-bold italic">Usado para gerenciar faturas automáticas de todos os escritórios.</p>
                                </div>
                                <div class="space-y-2">
                                    <x-input-label for="asas_mode" value="Modo do Sistema Asas" class="text-[10px] font-black uppercase text-slate-400" />
                                    <select name="asas_mode" id="asas_mode" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 text-xs font-black uppercase text-slate-700">
                                        <option value="sandbox">Sandbox (Teste)</option>
                                        <option value="production">Produção (Real)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Card 2: Matriz de Planos e Módulos -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Configuração de Planos & Módulos Liberados</h3>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-8 py-4 text-[10px] font-black text-black uppercase tracking-widest border-b border-slate-100 italic">Módulos do Sistema</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center">PLANO BASIC (Bronze)</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-amber-500 uppercase tracking-widest border-b border-slate-100 text-center">PLANO GOLD (Prata)</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-indigo-500 uppercase tracking-widest border-b border-slate-100 text-center">PLANO PLATINUM (Diamante)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                <tr class="bg-indigo-50/10">
                                    <td class="px-8 py-5 font-black text-indigo-600 uppercase text-[10px] tracking-tight">💰 Valor da Assinatura (Mensal)</td>
                                    <td class="px-8 py-5 text-center font-black text-slate-900 border-x border-slate-50">R$ 199,00</td>
                                    <td class="px-8 py-5 text-center font-black text-slate-900 border-x border-slate-50">R$ 399,00</td>
                                    <td class="px-8 py-5 text-center font-black text-slate-900 border-x border-slate-50">R$ 899,00</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5 font-bold text-slate-700 uppercase text-[10px] tracking-tight">📲 Automação WhatsApp (Disparos API)</td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5 font-bold text-slate-700 uppercase text-[10px] tracking-tight">📥 Importação Massiva (Lotes Excel)</td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" disabled class="rounded border-slate-200 text-slate-300 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5 font-bold text-slate-700 uppercase text-[10px] tracking-tight">🤖 Consultas de CPF Automatizadas (Bot)</td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" disabled class="rounded border-slate-200 text-slate-300 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" disabled class="rounded border-slate-200 text-slate-300 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5 font-bold text-slate-700 uppercase text-[10px] tracking-tight">📊 Relatórios de Performance Avançados</td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" disabled class="rounded border-slate-200 text-slate-300 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                    <td class="px-8 py-5 text-center"><input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500 h-5 w-5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-8 flex justify-end">
                    <x-primary-button class="px-16 py-5 bg-indigo-600 hover:bg-slate-900 rounded-3xl shadow-2xl shadow-indigo-500/20 text-sm font-black uppercase tracking-[0.2em] transition">
                        Atualizar Configurações Maestras
                    </x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

<script>
    const testPhoneInput = document.getElementById('test_phone');
    const hiddenPhoneInput = document.getElementById('hidden_test_phone');
    const testForm = document.getElementById('test-whatsapp-form');

    testForm.addEventListener('submit', function() {
        hiddenPhoneInput.value = testPhoneInput.value;
    });
</script>
