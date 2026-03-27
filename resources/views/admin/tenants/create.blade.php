<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tighter leading-tight flex items-center">
             <div class="p-2 bg-indigo-600 rounded-lg mr-3 shadow-lg shadow-indigo-500/20 text-white leading-none">
                <svg class="w-6 h-6 leading-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            CADASTRAR NOVO ESCRITÓRIO
        </h2>
    </x-slot>

    <div class="py-12 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-slate-100">
                <form action="{{ route('admin.tenants.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-10">
                        <!-- Dados da Empresa -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <x-input-label for="name" :value="__('Razão Social / Nome Fantasia')" class="text-xs font-black uppercase tracking-widest text-gray-400" />
                                <x-text-input id="name" name="name" type="text" class="w-full bg-slate-50 border-gray-100 rounded-2xl py-4" required placeholder="Ex: ABC Recuperadora de Crédito" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="slug" :value="__('Slug Único (Subdomínio/Identificador)')" class="text-xs font-black uppercase tracking-widest text-gray-400" />
                                <x-text-input id="slug" name="slug" type="text" class="w-full bg-slate-50 border-gray-100 rounded-2xl py-4" required placeholder="ex: abc-cobrancas" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-2">
                                <x-input-label for="document" :value="__('CNPJ / CPF do Escritório')" class="text-xs font-black uppercase tracking-widest text-gray-400" />
                                <x-text-input id="document" name="document" type="text" class="w-full bg-slate-50 border-gray-100 rounded-2xl py-4" required placeholder="00.000.000/0000-00" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="phone" :value="__('Telefone (WhatsApp)')" class="text-xs font-black uppercase tracking-widest text-gray-400" />
                                <x-text-input id="phone" name="phone" type="text" class="w-full bg-slate-50 border-gray-100 rounded-2xl py-4" required placeholder="558498888..." />
                            </div>
                            <div class="space-y-2">
                                <x-input-label for="email" :value="__('E-mail Administrativo')" class="text-xs font-black uppercase tracking-widest text-gray-400" />
                                <x-text-input id="email" name="email" type="email" class="w-full bg-slate-50 border-gray-100 rounded-2xl py-4" required placeholder="admin@escritorio.com" />
                            </div>
                        </div>

                        <!-- Configuração SaaS (Plano & Status) -->
                        <div class="pt-6 border-t border-gray-50 flex flex-col md:flex-row gap-8">
                            <div class="flex-1">
                                <x-input-label for="plan" :value="__('Plano do SaaS')" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <select id="plan" name="plan" class="w-full bg-indigo-50 border-none rounded-2xl py-4 font-black uppercase text-xs tracking-widest text-indigo-700">
                                    <option value="basic">Plano Básico (Mensal)</option>
                                    <option value="pro" selected>Plano Premium Pro (Mensal)</option>
                                    <option value="enterprise">Enterprise (Customizado)</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <x-input-label for="status" :value="__('Status Inicial')" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <select id="status" name="status" class="w-full bg-gray-50 border-none rounded-2xl py-4 font-black uppercase text-xs tracking-widest text-gray-700">
                                    <option value="active">Ativo Imediatamente</option>
                                    <option value="inactive">Aguardando Pagamento</option>
                                    <option value="suspended">Suspenso</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-8 flex justify-end">
                            <a href="{{ route('admin.tenants') }}" class="mr-4 px-8 py-4 bg-gray-50 text-gray-500 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-gray-100 transition">Cancelar</a>
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 px-10 py-5 rounded-3xl shadow-2xl shadow-indigo-500/20 font-black uppercase tracking-widest text-sm leading-none">
                                Confirmar e Criar Escritório
                            </x-primary-button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
