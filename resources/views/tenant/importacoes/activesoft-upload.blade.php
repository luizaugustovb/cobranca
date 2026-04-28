<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
            Importar PDF Activesoft
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Relação de Títulos de Cobrança — Activesoft Gestão Acadêmica</p>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-sm">
                <p class="font-bold mb-1">Erro ao processar o PDF:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 border border-gray-100 dark:border-gray-700">
                <form action="{{ route('tenant.importacoes.activesoft.preview') }}" method="POST" enctype="multipart/form-data" id="form-activesoft"
                    onsubmit="document.getElementById('btn-submit').disabled=true; document.getElementById('btn-submit').innerHTML='<svg class=\'animate-spin h-4 w-4 inline mr-2\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'></path></svg>Processando...';">
                    @csrf

                    <div class="mb-8">
                        <label class="flex flex-col items-center justify-center w-full h-52 border-2 border-dashed border-red-300 dark:border-red-700 rounded-3xl cursor-pointer bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 transition-all duration-200" id="drop-zone">
                            <div class="flex flex-col items-center justify-center text-center px-4">
                                <svg class="w-12 h-12 mb-3 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-300 font-semibold">
                                    <span class="text-red-600 font-black">Selecione o PDF</span> ou arraste aqui
                                </p>
                                <p id="file-name" class="text-xs font-bold text-red-600 mt-2 hidden"></p>
                                <p class="text-xs text-gray-400 mt-1">Apenas PDF Activesoft &mdash; max. 20 MB</p>
                            </div>
                            <input id="arquivo" name="arquivo" type="file" accept=".pdf" class="hidden" required
                                onchange="document.getElementById('file-name').textContent = this.files[0]?.name; document.getElementById('file-name').classList.remove('hidden');" />
                        </label>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-4 mb-8">
                        <p class="text-xs text-amber-700 dark:text-amber-400 font-semibold">
                            <strong>Formato suportado:</strong> "Relação de Títulos de Cobrança" exportada pelo <strong>Activesoft Gestão Acadêmica</strong>.<br>
                            O sistema extrairá responsáveis, endereços, alunos e parcelas automaticamente. Você poderá revisar e editar antes de confirmar.
                        </p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('tenant.importacoes') }}" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700">Cancelar</a>
                        <button type="submit" id="btn-submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-12 py-4 rounded-2xl shadow-xl text-sm font-black tracking-widest uppercase transition">
                            Processar PDF
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info sobre colunas --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="font-black text-sm uppercase tracking-widest text-slate-800 dark:text-white mb-4">O que será importado</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-600 dark:text-gray-400">
                    <div class="space-y-2">
                        <p class="font-black text-gray-700 dark:text-gray-300">Por Responsável:</p>
                        <ul class="space-y-1 pl-3 list-disc list-inside">
                            <li>Nome completo e CPF/CNPJ</li>
                            <li>Telefone / celular</li>
                            <li>Endereço completo (rua, nº, bairro, cidade, UF, CEP)</li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <p class="font-black text-gray-700 dark:text-gray-300">Por Parcela/Título:</p>
                        <ul class="space-y-1 pl-3 list-disc list-inside">
                            <li>Número do título + parcela</li>
                            <li>Situação (Em aberto / Pago)</li>
                            <li>Serviço e nome do aluno</li>
                            <li>Valor, multa/juros, valor a receber</li>
                            <li>Data de vencimento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>