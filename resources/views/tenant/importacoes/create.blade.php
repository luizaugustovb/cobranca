<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Nova Importação de Dados
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Siga o modelo padrão de planilha para evitar erros no processamento.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-10 border border-gray-100 dark:border-gray-700">
                <form action="{{ route('tenant.importacoes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-10">
                        <!-- Selection -->
                        <div>
                             <x-input-label for="tipo" :value="__('O que você deseja importar?')" class="text-sm font-black uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-4"/>
                             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex flex-col items-center bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900 border-2 border-transparent transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 shadow-sm">
                                    <input type="radio" name="tipo" value="devedores" class="sr-only" checked>
                                    <svg class="w-10 h-10 text-indigo-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span class="text-xs font-black uppercase tracking-widest">Devedores</span>
                                </label>
                                <label class="relative flex flex-col items-center bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900 border-2 border-transparent transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 shadow-sm">
                                    <input type="radio" name="tipo" value="titulos" class="sr-only">
                                    <svg class="w-10 h-10 text-emerald-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-xs font-black uppercase tracking-widest">Títulos</span>
                                </label>
                                <label class="relative flex flex-col items-center bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900 border-2 border-transparent transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 shadow-sm">
                                    <input type="radio" name="tipo" value="contratos" class="sr-only">
                                    <svg class="w-10 h-10 text-blue-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7M8 7h12m0 0v8a2 2 0 01-2 2h-2M9 21h6"/></svg>
                                    <span class="text-xs font-black uppercase tracking-widest">Contratos</span>
                                </label>
                             </div>
                             <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                        </div>

                        <!-- Dropzone Placeholder UI -->
                        <div class="space-y-4">
                             <x-input-label for="arquivo" :value="__('Selecione o arquivo (XLSX, CSV ou TXT)')" class="text-sm font-black uppercase tracking-widest text-slate-800 dark:text-gray-300"/>
                             <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-3xl cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-all duration-300">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-12 h-12 mb-4 text-gray-400 animate-bounce" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest"><span class="text-indigo-600">Clique para selecionar</span> ou arraste o arquivo</p>
                                    <p class="text-xs text-gray-400 font-medium">Tamanho máximo: 10MB</p>
                                </div>
                                <input id="arquivo" name="arquivo" type="file" class="hidden" required />
                            </label>
                            <x-input-error :messages="$errors->get('arquivo')" class="mt-2" />
                        </div>

                        <div class="flex justify-end pt-6">
                             <a href="{{ route('tenant.importacoes') }}" class="mr-4 px-8 py-4 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700">Cancelar</a>
                             <x-primary-button class="bg-slate-900 hover:bg-black px-12 py-5 rounded-2xl shadow-2xl text-lg font-black tracking-widest uppercase transition transform hover:scale-105 active:scale-95">
                                Iniciar Processamento
                             </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Download Model link -->
            <div class="mt-8 text-center text-gray-400">
                <p class="text-sm font-medium">Precisa do modelo? <a href="#" class="text-indigo-600 hover:underline font-black uppercase text-xs tracking-widest ml-1">Baixar Planilha Exemplo</a></p>
            </div>
        </div>
    </div>
</x-app-layout>
