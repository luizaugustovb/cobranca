<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('tenant.usuarios') }}" class="mr-4 p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-black text-lg sm:text-2xl text-slate-800 uppercase tracking-tighter">Novo Usuário</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl">
                        <p class="font-black text-xs uppercase tracking-widest mb-2">Corrija os erros:</p>
                        <ul class="text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tenant.usuarios.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-input-label for="name" value="Nome Completo" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                            <x-text-input id="name" name="name" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('name')" required placeholder="Nome do operador" />
                        </div>
                        <div class="space-y-2">
                            <x-input-label for="email" value="E-mail" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                            <x-text-input id="email" name="email" type="email" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('email')" required placeholder="email@escritorio.com" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-input-label for="phone" value="Telefone" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                            <x-text-input id="phone" name="phone" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('phone')" placeholder="(84) 99999-9999" />
                        </div>
                        <div class="space-y-2">
                            <x-input-label for="status" value="Status" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                            <select id="status" name="status" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 text-sm font-bold text-slate-700">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-input-label for="password" value="Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                            <x-text-input id="password" name="password" type="password" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" required placeholder="Mínimo 8 caracteres" />
                        </div>
                        <div class="space-y-2">
                            <x-input-label for="password_confirmation" value="Confirmar Senha" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" placeholder="Repita a senha" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-4">
                        <a href="{{ route('tenant.usuarios') }}" class="px-6 py-3 border border-slate-200 rounded-xl text-sm font-black text-slate-500 uppercase tracking-widest hover:bg-slate-50 transition">Cancelar</a>
                        <x-primary-button class="px-8 py-3 bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-500/20 font-black uppercase tracking-widest">
                            Criar Usuário
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
