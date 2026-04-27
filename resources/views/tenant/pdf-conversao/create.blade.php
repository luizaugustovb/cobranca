<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Leitor / Conversor de PDF
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Extraia tabelas de PDFs localmente — sem API externa.</p>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600 font-bold">{{ $message }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <button type="button" id="btn-modo-converter" onclick="setModo('converter')"
                    class="p-5 rounded-2xl border-2 border-blue-500 bg-blue-50 flex flex-col items-center gap-2 cursor-pointer transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-700">Converter para XLSX</span>
                    <span class="text-[9px] text-slate-400 text-center">Baixa o arquivo XLSX convertido</span>
                </button>
                <button type="button" id="btn-modo-leitura" onclick="setModo('leitura')"
                    class="p-5 rounded-2xl border-2 border-slate-200 bg-white flex flex-col items-center gap-2 cursor-pointer transition">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-600">Leitura Direta</span>
                    <span class="text-[9px] text-slate-400 text-center">Preview e importa sem converter</span>
                </button>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-5 sm:p-10 border border-gray-100">

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Engine de Extração</label>
                    <div class="grid grid-cols-2 gap-3" id="engine-selector">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-blue-400 bg-blue-50 cursor-pointer">
                            <input type="radio" name="engine_selector" value="pdfplumber" checked class="accent-blue-600">
                            <div>
                                <p class="text-xs font-black text-slate-700">pdfplumber</p>
                                <p class="text-[9px] text-slate-400">Melhor para PDFs com texto</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white cursor-pointer">
                            <input type="radio" name="engine_selector" value="tabula" class="accent-blue-600">
                            <div>
                                <p class="text-xs font-black text-slate-700">tabula-py</p>
                                <p class="text-[9px] text-slate-400">Melhor para tabelas complexas</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Arquivo PDF</label>
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-3xl cursor-pointer bg-gray-50 hover:bg-red-50 transition-all duration-200">
                        <div class="flex flex-col items-center text-center px-4">
                            <svg class="w-10 h-10 mb-3 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-semibold">
                                <span class="text-red-500 font-black">Clique para selecionar</span> ou arraste o PDF
                            </p>
                            <p class="text-xs text-gray-400 mt-1">Apenas PDF — máx. 20 MB</p>
                            <p id="file-name" class="text-xs text-red-600 font-bold mt-2 hidden"></p>
                        </div>
                        <input id="arquivo" type="file" accept=".pdf" class="hidden"
                            onchange="document.getElementById('file-name').textContent = this.files[0]?.name; document.getElementById('file-name').classList.remove('hidden');" />
                    </label>
                    @error('arquivo')
                        <p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div id="loading" class="hidden text-center py-4 mb-4">
                    <div class="inline-flex items-center gap-3 px-6 py-3 bg-blue-50 rounded-2xl">
                        <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="loading-text" class="text-sm font-black text-blue-600 uppercase tracking-widest">Processando...</span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="btn-acao" onclick="submitForm()"
                        class="inline-flex items-center gap-2 px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg transition transform hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span id="btn-label">Converter e Baixar</span>
                    </button>
                </div>

                <form id="form-converter" action="{{ route('tenant.pdf-conversao.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="arquivo" id="arquivo-converter" accept=".pdf">
                    <input type="hidden" name="engine" id="engine-converter">
                </form>
                <form id="form-leitura" action="{{ route('tenant.pdf-conversao.preview') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="arquivo" id="arquivo-leitura" accept=".pdf">
                    <input type="hidden" name="engine" id="engine-leitura">
                </form>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-3xl p-6 flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-blue-700 uppercase tracking-widest mb-1">Como usar</p>
                    <p class="text-xs text-blue-600">
                        <strong>Converter para XLSX:</strong> extrai tabelas do PDF e gera um .xlsx para download e importação manual.<br>
                        <strong>Leitura Direta:</strong> exibe preview das tabelas e permite importar diretamente para o sistema.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        let modoAtual = 'converter';

        function setModo(modo) {
            modoAtual = modo;
            const isConverter = modo === 'converter';

            document.getElementById('btn-modo-converter').className = isConverter
                ? 'p-5 rounded-2xl border-2 border-blue-500 bg-blue-50 flex flex-col items-center gap-2 cursor-pointer transition'
                : 'p-5 rounded-2xl border-2 border-slate-200 bg-white flex flex-col items-center gap-2 cursor-pointer transition';

            document.getElementById('btn-modo-leitura').className = !isConverter
                ? 'p-5 rounded-2xl border-2 border-emerald-500 bg-emerald-50 flex flex-col items-center gap-2 cursor-pointer transition'
                : 'p-5 rounded-2xl border-2 border-slate-200 bg-white flex flex-col items-center gap-2 cursor-pointer transition';

            document.getElementById('btn-label').textContent = isConverter ? 'Converter e Baixar' : 'Leitura Direta — Preview';

            const btn = document.getElementById('btn-acao');
            if (isConverter) {
                btn.className = btn.className.replace('bg-emerald-600 hover:bg-emerald-700', 'bg-blue-600 hover:bg-blue-700');
            } else {
                btn.className = btn.className.replace('bg-blue-600 hover:bg-blue-700', 'bg-emerald-600 hover:bg-emerald-700');
            }
        }

        function submitForm() {
            const fileInput   = document.getElementById('arquivo');
            const engineValue = document.querySelector('input[name="engine_selector"]:checked')?.value || 'pdfplumber';

            if (!fileInput.files.length) {
                alert('Selecione um arquivo PDF.');
                return;
            }

            document.getElementById('loading').classList.remove('hidden');
            const btn = document.getElementById('btn-acao');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            const dt = new DataTransfer();
            dt.items.add(fileInput.files[0]);

            if (modoAtual === 'converter') {
                document.getElementById('loading-text').textContent = 'Convertendo... aguarde';
                document.getElementById('arquivo-converter').files = dt.files;
                document.getElementById('engine-converter').value = engineValue;
                document.getElementById('form-converter').submit();
            } else {
                document.getElementById('loading-text').textContent = 'Lendo PDF... aguarde';
                document.getElementById('arquivo-leitura').files = dt.files;
                document.getElementById('engine-leitura').value = engineValue;
                document.getElementById('form-leitura').submit();
            }
        }

        document.querySelectorAll('input[name="engine_selector"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('#engine-selector label').forEach(l => {
                    l.classList.remove('border-blue-400', 'bg-blue-50');
                    l.classList.add('border-slate-200', 'bg-white');
                });
                this.closest('label').classList.remove('border-slate-200', 'bg-white');
                this.closest('label').classList.add('border-blue-400', 'bg-blue-50');
            });
        });
    </script>
</x-app-layout>
