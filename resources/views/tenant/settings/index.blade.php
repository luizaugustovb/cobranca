<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Configurações do Escritório
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Gerencie suas integrações e identidade visual.</p>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-8 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl shadow-sm" role="alert">
                <p class="font-bold">Sucesso!</p>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <form action="{{ route('tenant.settings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Aba Integrações -->
                    <div class="p-10 space-y-10">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-6 flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                Integrações de Pagamento (Asaas)
                            </h3>
                            <div class="space-y-4">
                                <x-input-label for="asaas_token" :value="__('API Access Token (Produção/Sandbox)')" class="text-xs font-bold uppercase tracking-widest text-gray-500" />
                                <x-text-input id="asaas_token" name="asaas_token" type="password" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 bg-gray-50 py-3" :value="$settings['asaas_token'] ?? ''" />
                                <p class="text-[10px] text-gray-400 font-medium italic">Obtenha seu token no painel do Asaas em Configurações > Integrações.</p>
                            </div>
                        </div>

                        <hr class="border-gray-50 dark:border-gray-700">

                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-6 flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                Notificações WhatsApp (Viicio)
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="viicio_token" :value="__('App Token')" class="text-xs font-bold uppercase tracking-widest text-gray-500" />
                                    <x-text-input id="viicio_token" name="viicio_token" type="password" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 bg-gray-50 py-3" :value="$settings['viicio_token'] ?? ''" />
                                    <p class="text-[10px] text-gray-400 font-medium italic mt-1">Token de autenticação da sua conta Viicio para disparar notificações aos devedores.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-50 dark:border-gray-700">

                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-6 flex items-center">
                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                Identidade Visual
                            </h3>
                            <div class="space-y-4">
                                <x-input-label for="company_name" :value="__('Nome de Exibição do Escritório')" class="text-xs font-bold uppercase tracking-widest text-gray-500" />
                                <x-text-input id="company_name" name="company_name" type="text" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 bg-gray-50 py-3" :value="$settings['company_name'] ?? ''" />

                                <div class="mt-4">
                                    <x-input-label for="logo" :value="__('Logo do Escritório')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                    <input type="file" name="logo" id="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-50 dark:border-gray-700">

                        {{-- Regras de Cobrança --}}
                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-2 flex items-center">
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 6v1m0 4v1m-6-6h.01M6 12h.01" />
                                    </svg>
                                </div>
                                Regras de Cobrança
                            </h3>
                            <p class="text-xs text-gray-400 mb-6 ml-11">Defina como os honorários são calculados automaticamente quando não informados na planilha de importação.</p>

                            <div class="space-y-5 ml-11">
                                {{-- Tipo --}}
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-3">Tipo de Honorários</label>
                                    <div class="grid grid-cols-2 gap-3" id="honorarios-tipo-selector">
                                        <label class="flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition
                                            {{ ($settings['honorarios_tipo'] ?? 'fixo') === 'fixo' ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 bg-gray-50 dark:bg-gray-700' }}">
                                            <input type="radio" name="honorarios_tipo" value="fixo" class="accent-blue-600"
                                                {{ ($settings['honorarios_tipo'] ?? 'fixo') === 'fixo' ? 'checked' : '' }}
                                                onchange="toggleHonorariosLabel(this.value)">
                                            <div>
                                                <p class="text-sm font-black text-slate-700 dark:text-slate-200">Valor Fixo (R$)</p>
                                                <p class="text-[10px] text-gray-400">Ex: R$ 50,00 por título</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition
                                            {{ ($settings['honorarios_tipo'] ?? 'fixo') === 'percentual' ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 bg-gray-50 dark:bg-gray-700' }}">
                                            <input type="radio" name="honorarios_tipo" value="percentual" class="accent-blue-600"
                                                {{ ($settings['honorarios_tipo'] ?? 'fixo') === 'percentual' ? 'checked' : '' }}
                                                onchange="toggleHonorariosLabel(this.value)">
                                            <div>
                                                <p class="text-sm font-black text-slate-700 dark:text-slate-200">Percentual (%)</p>
                                                <p class="text-[10px] text-gray-400">Ex: 10% do valor do débito</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Valor --}}
                                <div>
                                    <label for="honorarios_valor" id="honorarios-valor-label"
                                        class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                        {{ ($settings['honorarios_tipo'] ?? 'fixo') === 'percentual' ? 'Percentual (%)' : 'Valor Fixo (R$)' }}
                                    </label>
                                    <div class="relative max-w-xs">
                                        <span id="honorarios-prefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-black text-gray-400">
                                            {{ ($settings['honorarios_tipo'] ?? 'fixo') === 'percentual' ? '%' : 'R$' }}
                                        </span>
                                        <input id="honorarios_valor" name="honorarios_valor" type="number" step="0.01" min="0"
                                            value="{{ $settings['honorarios_valor'] ?? '0' }}"
                                            class="pl-10 w-full rounded-xl border-gray-200 dark:bg-gray-700 bg-gray-50 py-3 text-sm font-bold" />
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2">Deixe 0 para não aplicar honorários automaticamente. Se a planilha informar um valor na coluna <code class="bg-gray-100 px-1 rounded">honorarios</code>, ele prevalece sobre esta configuração.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 flex justify-end">
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-10 py-4 font-black rounded-2xl shadow-xl shadow-blue-500/20 uppercase tracking-widest">
                                Salvar Configurações
                            </x-primary-button>
                        </div>
                    </div>
                </form>

                <script>
                    function toggleHonorariosLabel(tipo) {
                        document.getElementById('honorarios-valor-label').textContent = tipo === 'percentual' ? 'Percentual (%)' : 'Valor Fixo (R$)';
                        document.getElementById('honorarios-prefix').textContent = tipo === 'percentual' ? '%' : 'R$';

                        document.querySelectorAll('#honorarios-tipo-selector label').forEach(l => {
                            l.classList.remove('border-blue-400', 'bg-blue-50');
                            l.classList.add('border-gray-200', 'bg-gray-50');
                        });
                        event.target.closest('label').classList.remove('border-gray-200', 'bg-gray-50');
                        event.target.closest('label').classList.add('border-blue-400', 'bg-blue-50');
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>