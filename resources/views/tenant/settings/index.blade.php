<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            ConfiguraÃ§Ãµes do EscritÃ³rio
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Gerencie suas integraÃ§Ãµes e identidade visual.</p>
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
                    
                    <!-- Aba IntegraÃ§Ãµes -->
                    <div class="p-10 space-y-10">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-6 flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                IntegraÃ§Ãµes de Pagamento (Asaas)
                            </h3>
                            <div class="space-y-4">
                                <x-input-label for="asaas_token" :value="__('API Access Token (ProduÃ§Ã£o/Sandbox)')" class="text-xs font-bold uppercase tracking-widest text-gray-500"/>
                                <x-text-input id="asaas_token" name="asaas_token" type="password" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 bg-gray-50 py-3" :value="$settings['asaas_token'] ?? ''" />
                                <p class="text-[10px] text-gray-400 font-medium italic">Obtenha seu token no painel do Asaas em ConfiguraÃ§Ãµes > IntegraÃ§Ãµes.</p>
                            </div>
                        </div>

                        <hr class="border-gray-50 dark:border-gray-700">

                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-6 flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                NotificaÃ§Ãµes WhatsApp (Viicio)
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="viicio_token" :value="__('App Token')" class="text-xs font-bold uppercase tracking-widest text-gray-500"/>
                                    <x-text-input id="viicio_token" name="viicio_token" type="password" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 bg-gray-50 py-3" :value="$settings['viicio_token'] ?? ''" />
                                    <p class="text-[10px] text-gray-400 font-medium italic mt-1">Token de autenticaÃ§Ã£o da sua conta Viicio para disparar notificaÃ§Ãµes aos devedores.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-50 dark:border-gray-700">

                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-gray-200 uppercase tracking-tighter mb-6 flex items-center">
                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                Identidade Visual
                            </h3>
                            <div class="space-y-4">
                                <x-input-label for="company_name" :value="__('Nome de ExibiÃ§Ã£o do EscritÃ³rio')" class="text-xs font-bold uppercase tracking-widest text-gray-500"/>
                                <x-text-input id="company_name" name="company_name" type="text" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 bg-gray-50 py-3" :value="$settings['company_name'] ?? ''" />
                                
                                <div class="mt-4">
                                    <x-input-label for="logo" :value="__('Logo do EscritÃ³rio')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2"/>
                                    <input type="file" name="logo" id="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 flex justify-end">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 px-10 py-4 font-black rounded-2xl shadow-xl shadow-indigo-500/20 uppercase tracking-widest">
                                Salvar ConfiguraÃ§Ãµes
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
