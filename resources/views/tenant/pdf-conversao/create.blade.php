<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Conversor PDF → XLSX
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Converta sua planilha em PDF para XLSX e depois importe as cobranças.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Card principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-10 border border-gray-100 dark:border-gray-700">

                {{-- Fluxo visual --}}
                <div class="flex items-center justify-center gap-3 mb-8">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900 flex items-center justify-center mb-1">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">PDF</span>
                    </div>
                    <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center mb-1">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">XLSX</span>
                    </div>
                </div>

                <form action="{{ route('tenant.pdf-conversao.store') }}" method="POST" enctype="multipart/form-data" id="form-conversao">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                                Arquivo PDF
                            </label>
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-3xl cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-gray-600 transition-all duration-200">
                                <div class="flex flex-col items-center text-center px-4">
                                    <svg class="w-10 h-10 mb-3 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                        <span class="text-red-500 font-black">Clique para selecionar</span> ou arraste o PDF
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">Apenas PDF — máx. 20 MB</p>
                                    <p id="file-name" class="text-xs text-red-600 font-bold mt-2 hidden"></p>
                                </div>
                                <input id="arquivo" name="arquivo" type="file" accept=".pdf" class="hidden" required
                                    onchange="document.getElementById('file-name').textContent = this.files[0]?.name; document.getElementById('file-name').classList.remove('hidden');" />
                            </label>
                            @error('arquivo')
                                <p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Loading state --}}
                        <div id="loading" class="hidden text-center py-4">
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm font-black text-indigo-600 dark:text-indigo-300 uppercase tracking-widest">Convertendo... aguarde</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2 gap-4">
                            <p class="text-xs text-gray-400">
                                O download do XLSX iniciará automaticamente.
                            </p>
                            <button type="submit" id="btn-converter"
                                class="inline-flex items-center gap-2 px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition transform hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Converter e Baixar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Dica próximo passo --}}
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-3xl p-6 flex items-start gap-4">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-800 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-widest mb-1">Próximo passo</p>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400">
                        Após baixar o XLSX, ajuste as colunas conforme o modelo de importação e
                        <a href="{{ route('tenant.importacoes.create') }}" class="font-black underline hover:no-underline">importe o arquivo aqui</a>.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('form-conversao').addEventListener('submit', function () {
            document.getElementById('btn-converter').disabled = true;
            document.getElementById('btn-converter').classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('loading').classList.remove('hidden');
        });
    </script>
</x-app-layout>
