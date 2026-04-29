<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
            {{ isset($cliente) ? 'Editar Cliente' : 'Novo Cliente' }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-5 sm:p-8 border border-gray-100 dark:border-gray-700">
                <form action="{{ isset($cliente) ? route('tenant.clientes.update', $cliente) : route('tenant.clientes.store') }}" method="POST">
                    @csrf
                    @if(isset($cliente)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <x-input-label for="nome" :value="__('Nome / Razão Social')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('nome', $cliente->nome ?? '')" required />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="documento" :value="__('CPF / CNPJ')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('documento', $cliente->documento ?? '')" required />
                            <x-input-error :messages="$errors->get('documento')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('email', $cliente->email ?? '')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone" :value="__('Telefone')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('telefone', $cliente->telefone ?? '')" />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="endereco" :value="__('Endereço')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('endereco', $cliente->endereco ?? '')" />
                            <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Taxas de cobrança --}}
                    <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-8">
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-5">Taxas de Cobrança</p>
                        <p class="text-xs text-gray-400 mb-6">
                            Estas taxas serão usadas para calcular o <strong>valor corrigido</strong> dos títulos deste cliente.
                            Juros são acumulados mensalmente a partir da data de vencimento. Deixe em <strong>0</strong> para usar a configuração global.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="multa_percentual" :value="__('Multa (%)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <div class="relative">
                                    <x-text-input id="multa_percentual" name="multa_percentual" type="number" step="0.01" min="0" max="100"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-amber-400 focus:ring-amber-400 bg-gray-50 dark:bg-gray-700 py-3 pr-10"
                                        :value="old('multa_percentual', $cliente->multa_percentual ?? '0')" />
                                    <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 font-bold text-sm mt-1">%</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Aplicada uma vez no vencimento.</p>
                                <x-input-error :messages="$errors->get('multa_percentual')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="juros_mensal" :value="__('Juros Mensal (%)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <div class="relative">
                                    <x-text-input id="juros_mensal" name="juros_mensal" type="number" step="0.01" min="0" max="100"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-amber-400 focus:ring-amber-400 bg-gray-50 dark:bg-gray-700 py-3 pr-10"
                                        :value="old('juros_mensal', $cliente->juros_mensal ?? '0')" />
                                    <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 font-bold text-sm mt-1">%</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Acumulado por mês desde o vencimento.</p>
                                <x-input-error :messages="$errors->get('juros_mensal')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="honorarios_percentual" :value="__('Honorários (%)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <div class="relative">
                                    <x-text-input id="honorarios_percentual" name="honorarios_percentual" type="number" step="0.01" min="0" max="100"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-amber-400 focus:ring-amber-400 bg-gray-50 dark:bg-gray-700 py-3 pr-10"
                                        :value="old('honorarios_percentual', $cliente->honorarios_percentual ?? '0')" />
                                    <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 font-bold text-sm mt-1">%</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">0 = usa configuração global.</p>
                                <x-input-error :messages="$errors->get('honorarios_percentual')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="ipca_mensal" :value="__('IPCA Mensal (%)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <div class="relative">
                                    <x-text-input id="ipca_mensal" name="ipca_mensal" type="number" step="0.01" min="0" max="100"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-amber-400 focus:ring-amber-400 bg-gray-50 dark:bg-gray-700 py-3 pr-10"
                                        :value="old('ipca_mensal', $cliente->ipca_mensal ?? '0')" />
                                    <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 font-bold text-sm mt-1">%</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Correção monetária mensal equivalente ao IPCA (ex: 0.38).</p>
                                <x-input-error :messages="$errors->get('ipca_mensal')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-8 py-4 rounded-xl shadow-xl shadow-blue-500/20 font-extrabold uppercase tracking-widest">
                            {{ isset($cliente) ? 'Salvar Alterações' : 'Cadastrar Cliente' }}
                        </x-primary-button>
                        <a href="{{ route('tenant.clientes') }}" class="inline-flex items-center px-8 py-4 bg-white border border-gray-300 rounded-xl font-extrabold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>