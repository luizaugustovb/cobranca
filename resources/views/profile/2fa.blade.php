<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white uppercase tracking-tighter">
            SeguranÃ§a: AutenticaÃ§Ã£o em Duas Etapas (2FA)
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-5 sm:p-10 border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row items-center gap-10">
                    
                    <!-- QR Code Placeholder (System should pass URI) -->
                    <div class="flex-shrink-0 p-4 bg-white rounded-3xl shadow-inner border border-gray-50 flex items-center justify-center">
                        @if(auth()->user()->google2fa_secret)
                            <!-- Real QR Code should be rendered here with a library or helper -->
                            <div class="w-48 h-48 bg-slate-900 flex items-center justify-center text-white text-[10px] uppercase font-black text-center p-4">
                                [ QR CODE ATIVO ]<br>Escaneie no Google Authenticator
                            </div>
                        @else
                            <div class="w-48 h-48 bg-slate-100 flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 space-y-6">
                        <h4 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter">Proteja sua conta</h4>
                        <p class="text-sm text-gray-500 font-medium">A autenticaÃ§Ã£o em duas etapas adiciona uma camada extra de seguranÃ§a Ã  sua conta, exigindo mais do que apenas uma senha para entrar.</p>
                        
                        <div class="pt-6">
                            @if(!auth()->user()->google2fa_secret)
                                <form method="POST" action="#"> <!-- Rota de ativaÃ§Ã£o 2FA -->
                                    @csrf
                                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-8 py-4 rounded-xl shadow-lg shadow-blue-500/20 font-black uppercase tracking-widest leading-none">
                                        Ativar 2FA Agora
                                    </x-primary-button>
                                </form>
                            @else
                                <div class="flex items-center text-green-600 font-black uppercase text-xs tracking-widest mb-6">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    AutenticaÃ§Ã£o Ativada
                                </div>
                                <form method="POST" action="#">
                                    @csrf
                                    <button class="text-xs font-black text-red-500 hover:text-red-700 uppercase tracking-widest">Desativar 2FA</button>
                                </form>
                            @else
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
