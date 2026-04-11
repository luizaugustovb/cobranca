<x-guest-layout>
    <!-- Logo & Title -->
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Entrar no <span class="text-indigo-600">CobranÃ§aPro</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">Portal de RecuperaÃ§Ã£o de Ativos</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ showPassword: false }">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" value="Seu E-mail" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="email" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contato@empresa.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 px-1">
                <x-input-label for="password" value="Sua Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-black uppercase tracking-widest text-indigo-500 hover:text-indigo-700 transition underline decoration-dotted capitalize" href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <div class="relative group">
                <x-text-input id="password" 
                                class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm pr-12"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                required autocomplete="current-password" 
                                placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" />
                
                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-indigo-600 focus:outline-none transition">
                    <template x-if="!showPassword">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </template>
                    <template x-if="showPassword">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.046m2.458-2.458A9.954 9.954 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-2.102-2.102L3 3"/></svg>
                    </template>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center px-1">
            <input id="remember_me" type="checkbox" class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 shadow-sm focus:ring-indigo-500 transition" name="remember">
            <span class="ms-3 text-[11px] font-black text-slate-500 uppercase tracking-widest">Manter Conectado</span>
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full py-5 bg-indigo-600 hover:bg-slate-900 rounded-2xl shadow-xl shadow-indigo-500/20 flex justify-center text-sm font-black uppercase tracking-[0.2em] transition translate-y-0 active:translate-y-1">
                Acessar Plataforma
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
