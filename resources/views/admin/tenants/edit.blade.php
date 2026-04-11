<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.tenants') }}" class="p-2 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-indigo-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-black text-lg sm:text-2xl text-slate-900 tracking-tighter uppercase leading-none">
                Editar Escritório: <span class="text-indigo-600">{{ $tenant->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 px-6 py-5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start space-x-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-xs font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 px-6 py-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <p class="text-xs font-bold text-rose-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="p-6 sm:p-10 space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Seção: Identificação -->
                    <div>
                        <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-6 flex items-center">
                            <span class="w-8 h-px bg-indigo-100 mr-3"></span> Identificação Básica
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="name" value="Nome do Escritório" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="name" name="name" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('name', $tenant->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="slug" value="Slug Único" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="slug" name="slug" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('slug', $tenant->slug)" required />
                                <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="document" value="CNPJ / CPF" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="document" name="document" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('document', $tenant->document)" required />
                                <x-input-error :messages="$errors->get('document')" class="mt-1" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="phone" value="Telefone (WhatsApp)" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="phone" name="phone" type="text" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('phone', $tenant->phone)" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <x-input-label for="email" value="E-mail Administrativo" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="email" name="email" type="email" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4" :value="old('email', $tenant->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Plano e Status -->
                    <div class="pt-6 border-t border-slate-50">
                        <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-6 flex items-center">
                            <span class="w-8 h-px bg-indigo-100 mr-3"></span> Configurações de Operação
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="plan" value="Plano de Assinatura" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <select name="plan" id="plan" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 text-sm font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->slug }}" {{ $tenant->plan == $plan->slug ? 'selected' : '' }}>{{ $plan->nome }} — R$ {{ number_format($plan->valor, 2, ',', '.') }}/mês</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="status" value="Status do Acesso" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <select name="status" id="status" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 text-sm font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    <option value="active" {{ $tenant->status == 'active' ? 'selected' : '' }}>Ativo (Liberado)</option>
                                    <option value="inactive" {{ $tenant->status == 'inactive' ? 'selected' : '' }}>Bloqueado (Inativo)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Integração WhatsApp (Viicio) -->
                    <div class="pt-6 border-t border-slate-50 bg-indigo-50/30 -mx-10 px-10 pb-10 mt-6">
                        <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] py-6 flex items-center">
                            <span class="w-8 h-px bg-indigo-200 mr-3"></span> Integração Viicio (WhatsApp)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="viicio_token" value="Token de Conexão do Escritório" class="text-[10px] font-black uppercase tracking-widest text-slate-400" />
                                <x-text-input id="viicio_token" name="viicio_token" type="text" class="w-full bg-white border-slate-200 rounded-2xl py-4" :value="old('viicio_token', $tenant->viicio_token)" placeholder="Token Bearer da API Viicio" />
                                <p class="text-[9px] text-slate-400 font-medium italic mt-1">* Se vazio, usará o Token Master do sistema para disparos.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé de Ações -->
                    <div class="pt-10 flex items-center justify-between">
                        <button type="button" onclick="window.history.back()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">Cancelar Alterações</button>
                        <div class="flex items-center gap-4">
                            <button type="button" @click="$dispatch('open-reset-modal')" class="px-6 py-4 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">
                                🔑 Resetar Senha
                            </button>
                            <x-primary-button class="px-12 py-5 bg-indigo-600 hover:bg-slate-900 rounded-2xl shadow-xl shadow-indigo-500/20 text-sm font-black uppercase tracking-[0.2em] transition">
                                Salvar Alterações
                            </x-primary-button>
                        </div>
                    </div>
                </form>

                <!-- Modal de Confirmação de Reset -->
            </div>
        </div>
    </div>

    <!-- Modal fora do overflow-hidden para renderizar corretamente sobre tudo -->
    <div x-data="{ open: false }" x-on:open-reset-modal.window="open = true">
    <div x-show="open" x-transition.opacity style="display:none" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full">
            <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-2">Confirmar Reset de Senha</h3>
            <p class="text-xs text-slate-500 font-semibold mb-1">A senha do escritório <strong>{{ $tenant->name }}</strong> será redefinida para:</p>
            <p class="text-lg font-black text-indigo-600 tracking-widest my-3">Admin@123</p>
            <p class="text-[10px] text-slate-400 mb-6">O gestor será obrigado a trocar a senha no próximo acesso. {{ $tenant->phone ? 'Um WhatsApp será enviado para ' . $tenant->phone . '.' : 'Nenhum WhatsApp será enviado (telefone não cadastrado).' }}</p>
            <div class="flex gap-3">
                <button type="button" @click="open = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Cancelar</button>
                <form action="{{ route('admin.tenants.reset-password', $tenant) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition shadow-lg shadow-amber-500/20">
                        Confirmar Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
