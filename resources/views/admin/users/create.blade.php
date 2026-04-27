<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users') }}" class="p-2 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-emerald-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-black text-lg sm:text-2xl text-slate-900 tracking-tighter uppercase leading-none">
                Designar Novo <span class="text-emerald-600">Master</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <form action="{{ route('admin.users.store') }}" method="POST" class="p-10 space-y-8">
                    @csrf
                    
                    <div>
                        <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100 mb-8 flex items-start space-x-4">
                            <div class="text-amber-500 mt-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <p class="text-xs font-bold text-amber-900 uppercase tracking-widest leading-loose">
                                Cuidado Extremo: A conta criada terá poderes supremos ("Deus") sobre o software, incluindo faturas, inquilinos (Tenants) e acesso absoluto a chaves de API.
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-6 flex items-center">
                            <span class="w-8 h-px bg-emerald-200 mr-3"></span> Perfil do Novo Executivo
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="name" value="Nome do Executivo Master" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="name" name="name" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="email" value="E-mail de Acesso Root" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="email" name="email" type="email" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="document" value="CPF Seguro" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="document" name="document" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('document')" placeholder="Recuperação (Opcional)" />
                                <x-input-error :messages="$errors->get('document')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="phone" value="Telefone Seguro (WhatsApp)" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="phone" name="phone" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('phone')" placeholder="Recuperação (Opcional)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <x-input-label for="password" value="Senha Inicial do Executivo" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="password" name="password" type="password" class="w-full bg-blue-50 border-blue-100/50 rounded-2xl py-4" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 flex flex-col md:flex-row items-center justify-between border-t border-slate-50">
                        <button type="button" onclick="window.history.back()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition mb-6 md:mb-0">Cancelar Elevação</button>
                        <x-primary-button class="px-12 py-5 bg-emerald-600 hover:bg-slate-900 rounded-2xl shadow-xl shadow-emerald-500/20 text-sm font-black uppercase tracking-[0.2em] transition">
                            Instanciar Administrador
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
