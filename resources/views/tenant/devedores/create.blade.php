<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
            {{ isset($devedor) ? 'Editar Devedor' : 'Novo Devedor' }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-5 sm:p-8 border border-gray-100 dark:border-gray-700">
                <form action="{{ isset($devedor) ? route('tenant.devedores.update', $devedor) : route('tenant.devedores.store') }}" method="POST">
                    @csrf
                    @if(isset($devedor)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <x-input-label for="cliente_id" :value="__('Pertence ao Cliente (Carteira)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <select id="cliente_id" name="cliente_id" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" required>
                                <option value="">Selecione o Cliente</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $devedor->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }} ({{ $cliente->documento }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nome" :value="__('Nome do Devedor')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3 text-lg font-bold" :value="old('nome', $devedor->nome ?? '')" required />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cpf_cnpj" :value="__('CPF / CNPJ')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="cpf_cnpj" name="cpf_cnpj" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3 text-lg font-mono font-bold tracking-widest" :value="old('cpf_cnpj', $devedor->cpf_cnpj ?? '')" required />
                            <x-input-error :messages="$errors->get('cpf_cnpj')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail do Devedor')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('email', $devedor->email ?? '')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone" :value="__('Telefone / WhatsApp')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('telefone', $devedor->telefone ?? '')" />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 pb-2 border-b border-gray-100">Endereço</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2">
                                <x-input-label for="rua" :value="__('Logradouro (Rua / Av.)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <x-text-input id="rua" name="rua" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('rua', $devedor->rua ?? '')" />
                                <x-input-error :messages="$errors->get('rua')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="numero" :value="__('Número')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('numero', $devedor->numero ?? '')" />
                                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('bairro', $devedor->bairro ?? '')" />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('cidade', $devedor->cidade ?? '')" />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="estado" :value="__('Estado (UF)')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <x-text-input id="estado" name="estado" type="text" maxlength="2" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3 uppercase" :value="old('estado', $devedor->estado ?? '')" />
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2" />
                                <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3 font-mono" :value="old('cep', $devedor->cep ?? '')" />
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex space-x-4">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-8 py-4 rounded-xl shadow-xl shadow-blue-500/20 font-extrabold uppercase tracking-widest">
                            {{ isset($devedor) ? 'Salvar Alterações' : 'Cadastrar Devedor' }}
                        </x-primary-button>
                        <a href="{{ route('tenant.devedores') }}" class="inline-flex items-center px-8 py-4 bg-white border border-gray-300 rounded-xl font-extrabold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>