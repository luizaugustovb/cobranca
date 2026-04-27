<x-guest-layout>
    <div class="mb-10 text-center">
        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mx-auto mb-6 shadow-lg shadow-emerald-500/10">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </div>
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Recuperar com WhatsApp</h2>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2 px-6">Informe seu CPF e Celular para receber uma nova senha temporária via WhatsApp.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.whatsapp') }}" class="space-y-6">
        @csrf

        <!-- CPF / Documento -->
        <div class="space-y-2">
            <x-input-label for="document" :value="__('Seu CPF')" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="document" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-emerald-500 focus:border-emerald-500" type="text" name="document" :value="old('document')" required autofocus placeholder="000.000.000-00" />
            <x-input-error :messages="$errors->get('document')" class="mt-2 text-[10px]" />
        </div>

        <!-- Phone / WhatsApp -->
        <div class="space-y-2">
            <x-input-label for="phone" :value="__('WhatsApp (DDI+DDD+NÚMERO)')" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="phone" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-emerald-500 focus:border-emerald-500" type="text" name="phone" :value="old('phone')" required placeholder="5581999990000" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2 text-[10px]" />
        </div>

        <div class="flex flex-col space-y-4 pt-4">
            <x-primary-button class="w-full py-5 bg-emerald-600 hover:bg-emerald-700 rounded-2xl shadow-xl shadow-emerald-500/20 flex justify-center text-sm font-black uppercase tracking-[0.2em] transition">
                Enviar Senha por WhatsApp
            </x-primary-button>
            <a href="{{ route('login') }}" class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition">Voltar para o Login</a>
        </div>
    </form>
</x-guest-layout>
