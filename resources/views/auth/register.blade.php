<x-guest-layout>
    <!-- Logo & Title -->
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Criar Conta no <span class="text-indigo-600">CobranÃ§aPro</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2 px-6">Comece sua jornada na plataforma de recuperaÃ§Ã£o de ativos n.Âº 1 do Brasil.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <x-input-label for="name" value="Nome Completo" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="name" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition" type="text" name="name" :value="old('name')" required autofocus placeholder="Ex: JoÃ£o da Silva" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Grid de Contato -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Email Address -->
            <div class="space-y-2">
                <x-input-label for="email" value="E-mail Corporativo" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                <x-text-input id="email" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition" type="email" name="email" :value="old('email')" required placeholder="joao@empresa.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- CPF (Documento) -->
            <div class="space-y-2">
                <x-input-label for="document" value="CPF" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                <x-text-input id="document" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition" type="text" name="document" :value="old('document')" required placeholder="000.000.000-00" />
                <x-input-error :messages="$errors->get('document')" class="mt-1" />
            </div>
        </div>

        <!-- WhatsApp / Telefone -->
        <div class="space-y-2">
            <x-input-label for="phone" value="WhatsApp (DDI + DDD + NÃšMERO)" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
            <x-text-input id="phone" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition" type="text" name="phone" :value="old('phone')" required placeholder="5581999990000" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        <!-- Grid de Senha -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Password -->
            <div class="space-y-2">
                <x-input-label for="password" value="Criar Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                <x-text-input id="password" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                type="password"
                                name="password"
                                required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <x-input-label for="password_confirmation" value="Confirmar Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                <x-text-input id="password_confirmation" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                type="password"
                                name="password_confirmation" required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="pt-6 flex flex-col space-y-4">
            <x-primary-button class="w-full py-5 bg-indigo-600 hover:bg-slate-900 rounded-2xl shadow-xl shadow-indigo-500/20 flex justify-center text-sm font-black uppercase tracking-[0.2em] transition">
                Criar Minha Conta
            </x-primary-button>
            <a href="{{ route('login') }}" class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-indigo-600 transition underline decoration-dotted capitalize">JÃ¡ possui conta? Entrar agora</a>
        </div>
    </form>
</x-guest-layout>
