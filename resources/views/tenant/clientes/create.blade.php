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
                            <x-input-label for="nome" :value="__('Nome / RazÃ£o Social')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2"/>
                            <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('nome', $cliente->nome ?? '')" required />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="documento" :value="__('CPF / CNPJ')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2"/>
                            <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('documento', $cliente->documento ?? '')" required />
                            <x-input-error :messages="$errors->get('documento')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2"/>
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('email', $cliente->email ?? '')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefone" :value="__('Telefone')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2"/>
                            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('telefone', $cliente->telefone ?? '')" />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="endereco" :value="__('EndereÃ§o')" class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2"/>
                            <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 py-3" :value="old('endereco', $cliente->endereco ?? '')" />
                            <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-10 flex space-x-4">
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-8 py-4 rounded-xl shadow-xl shadow-blue-500/20 font-extrabold uppercase tracking-widest">
                            {{ isset($cliente) ? 'Salvar AlteraÃ§Ãµes' : 'Cadastrar Cliente' }}
                        </x-primary-button>
                        <a href="{{ route('tenant.clientes') }}" class="inline-flex items-center px-8 py-4 bg-white border border-gray-300 rounded-xl font-extrabold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
