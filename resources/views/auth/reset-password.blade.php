<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Nova <span class="text-blue-600">Senha</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2 px-6">Defina seu novo acesso de segurança.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" value="Confirmar E-mail" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="email" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <x-input-label for="password" value="Sua Nova Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="password" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <x-input-label for="password_confirmation" value="Repetir Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="password_confirmation" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-6">
            <x-primary-button class="w-full py-5 bg-blue-600 hover:bg-slate-900 rounded-2xl shadow-xl shadow-blue-500/20 flex justify-center text-sm font-black uppercase tracking-[0.2em] transition">
                {{ __('Redefinir Senha') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
