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

                        <hr class="border-gray-50 dark:border-gray-700">

                        {{-- Mensagem de Cobrança WhatsApp --}}
                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-2 flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.171.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.512-2.961-2.628-.086-.117-.704-.933-.704-1.782 0-.85.433-1.268.587-1.442.155-.174.337-.217.45-.217l.323.004c.103.005.23.02.361.33.136.323.466 1.137.507 1.219.04.083.067.18.013.287-.054.107-.081.174-.162.27-.081.094-.17.21-.242.282-.081.082-.166.171-.072.332.094.162.418.689.897 1.115.617.551 1.137.721 1.3.8.163.078.261.066.359-.045.099-.112.424-.492.537-.66.113-.168.225-.141.38-.084.155.057.986.465 1.155.549.169.085.281.127.322.197.041.07.041.405-.103.81z" />
                                    </svg>
                                </div>
                                Mensagem de Cobrança (WhatsApp)
                            </h3>
                            <p class="text-xs text-gray-400 mb-6 ml-11">
                                Texto enviado ao devedor ao clicar no botão WhatsApp da lista de títulos em aberto.<br>
                                <span class="font-bold text-gray-500">Variáveis disponíveis:</span>
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{nome}</code> nome do devedor,
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{numero}</code> nº do título,
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{valor}</code> valor total,
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{vencimento}</code> data de vencimento.
                            </p>
                            <div class="ml-11">
                                <textarea id="whatsapp_cobranca_texto" name="whatsapp_cobranca_texto" rows="4"
                                    class="w-full rounded-xl border-gray-200 dark:bg-gray-700 bg-gray-50 py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-200 resize-none"
                                    placeholder="Ola {nome}, consta em nosso sistema o titulo #{numero} no valor de R$ {valor} com vencimento em {vencimento}. Entre em contato para regularizacao.">{{ $settings['whatsapp_cobranca_texto'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <hr class="border-gray-50 dark:border-gray-700">

                        {{-- Mensagem de Autoatendimento (Ficha do Devedor) --}}
                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-2 flex items-center">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.171.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.512-2.961-2.628-.086-.117-.704-.933-.704-1.782 0-.85.433-1.268.587-1.442.155-.174.337-.217.45-.217l.323.004c.103.005.23.02.361.33.136.323.466 1.137.507 1.219.04.083.067.18.013.287-.054.107-.081.174-.162.27-.081.094-.17.21-.242.282-.081.082-.166.171-.072.332.094.162.418.689.897 1.115.617.551 1.137.721 1.3.8.163.078.261.066.359-.045.099-.112.424-.492.537-.66.113-.168.225-.141.38-.084.155.057.986.465 1.155.549.169.085.281.127.322.197.041.07.041.405-.103.81z" />
                                    </svg>
                                </div>
                                Mensagem de Autoatendimento (Ficha do Devedor)
                            </h3>
                            <p class="text-xs text-gray-400 mb-6 ml-11">
                                Texto enviado quando o botão WhatsApp é clicado na ficha do devedor, resumindo todos os débitos em aberto.<br>
                                <span class="font-bold text-gray-500">Variáveis disponíveis:</span>
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{nome}</code> primeiro nome,
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{qtd}</code> quantidade de títulos,
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{total}</code> valor total em aberto.
                            </p>
                            <div class="ml-11">
                                <textarea id="whatsapp_autoatendimento_texto" name="whatsapp_autoatendimento_texto" rows="4"
                                    class="w-full rounded-xl border-gray-200 dark:bg-gray-700 bg-gray-50 py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-200 resize-none"
                                    placeholder="Olá {nome}, identificamos débito(s) em seu cadastro em nosso sistema. Entre em contato conosco para regularizar sua situação e negociar as condições de pagamento.">{{ $settings['whatsapp_autoatendimento_texto'] ?? '' }}</textarea>
                            </div>
                        </div>
                        {{-- Disparo Mensal Automático --}}
                        <div class="pt-8 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-2 flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                Disparo Mensal Automático
                            </h3>
                            <p class="text-xs text-gray-400 mb-6 ml-11">
                                Quando ativado, o sistema envia automaticamente uma mensagem WhatsApp todo mês
                                no dia escolhido para todos os devedores com títulos em aberto.<br>
                                <span class="font-bold text-gray-500">Variáveis:</span>
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{nome}</code> primeiro nome,
                                <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{qtd}</code> quantidade de títulos em aberto.
                            </p>
                            <div class="ml-11 space-y-5">
                                {{-- Toggle ativo --}}
                                <label class="flex items-center gap-4 cursor-pointer select-none"
                                    x-data="{ ativo: {{ ($settings['disparo_mensal_ativo'] ?? '0') === '1' ? 'true' : 'false' }} }">
                                    <input type="hidden" name="disparo_mensal_ativo" value="0">
                                    <input type="checkbox" name="disparo_mensal_ativo" value="1" class="sr-only peer"
                                        x-model="ativo">
                                    <div class="relative w-12 h-6 rounded-full transition-colors duration-200 shrink-0 cursor-pointer"
                                        :class="ativo ? 'bg-emerald-100' : 'bg-red-100'"
                                        @click="ativo = !ativo">
                                        <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full shadow transition-all duration-200"
                                            :class="ativo ? 'translate-x-6 bg-emerald-500' : 'translate-x-0 bg-red-500'"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black" :class="ativo ? 'text-emerald-700' : 'text-slate-400'"
                                            x-text="ativo ? 'Disparo mensal habilitado' : 'Disparo mensal desabilitado'"></p>
                                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">Ativa o envio automático todo mês.</p>
                                    </div>
                                </label>

                                {{-- Dia do mês --}}
                                <div class="flex items-center gap-3">
                                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest w-36">Dia do mês</label>
                                    <input type="number" name="disparo_mensal_dia"
                                        value="{{ $settings['disparo_mensal_dia'] ?? 5 }}"
                                        min="1" max="28"
                                        class="w-20 rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-700 py-2 px-3 text-sm font-mono focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-xs text-gray-400">de cada mês (1–28)</span>
                                </div>

                                {{-- Texto da mensagem mensal --}}
                                <textarea name="whatsapp_mensal_texto" rows="4"
                                    class="w-full rounded-xl border-gray-200 dark:bg-gray-700 bg-gray-50 py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-200 resize-none"
                                    placeholder="Olá {nome}, identificamos {qtd} débito(s) em seu cadastro. Entre em contato conosco para regularizar sua situação e negociar as condições de pagamento.">{{ $settings['whatsapp_mensal_texto'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-10 py-4 font-black rounded-2xl shadow-xl shadow-blue-500/20 uppercase tracking-widest">
                            Salvar Configurações
                        </x-primary-button>
                    </div>
                </form>
            </div>

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
</x-app-layout>