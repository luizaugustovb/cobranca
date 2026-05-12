<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white">Nova Importacao de Cobrancas</h2>
    </x-slot>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-5 sm:p-10 border border-gray-100 dark:border-gray-700">
                {{-- Form principal: XLSX/CSV → importar --}}
                <form id="form-xlsx" action="{{ route('tenant.importacoes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="arquivo" id="arquivo-xlsx" accept=".xlsx,.xls,.csv" class="hidden">
                    <input type="hidden" name="cliente_id" id="xlsx-cliente-id">
                </form>

                {{-- Form PDF: leitura direta --}}
                <form id="form-pdf" action="{{ route('tenant.pdf-conversao.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="arquivo" id="arquivo-pdf" accept=".pdf" class="hidden">
                    <input type="hidden" name="engine" value="pdfplumber">
                </form>

                <div class="space-y-6">
                    {{-- Seleção do Cliente --}}
                    <div>
                        <label class="block text-sm font-black uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2">
                            Cliente (Contratante) <span class="text-red-500">*</span>
                        </label>
                        <select id="select-cliente"
                            class="w-full rounded-2xl border-gray-200 bg-gray-50 dark:bg-gray-700 py-3 px-4 text-sm font-semibold focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('xlsx-cliente-id').value = this.value">
                            <option value="">— Selecione para qual cliente é esta planilha —</option>
                            @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->nome }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        <p class="text-xs text-gray-400 mt-1">Os devedores importados serão vinculados ao cliente selecionado.</p>
                    </div>

                    <div class="space-y-4">
                        <label id="upload-label" class="block text-sm font-black uppercase tracking-widest text-slate-800 dark:text-gray-300">Arquivo XLSX, CSV ou PDF</label>
                        <label class="flex flex-col items-center justify-center w-full h-56 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-3xl cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-gray-600 transition-all duration-200" id="drop-zone">
                            <div class="flex flex-col items-center justify-center text-center px-4">
                                <svg id="upload-icon" class="w-12 h-12 mb-3 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                    <span id="upload-cta" class="text-blue-600 font-black">Clique para selecionar</span> ou arraste o arquivo
                                </p>
                                <p id="upload-hint" class="text-xs text-gray-400 mt-1">XLSX, XLS, CSV ou PDF &mdash; max. 20 MB</p>
                                <p id="file-name" class="text-xs font-bold mt-2 hidden"></p>
                                <span id="badge-pdf" class="hidden mt-2 inline-block text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600 px-3 py-1 rounded-full">PDF detectado — leitura direta ativada</span>
                            </div>
                            <input id="arquivo" type="file" accept=".xlsx,.xls,.csv,.pdf" class="hidden" required onchange="onFileChange(this)" />
                        </label>
                        <x-input-error :messages="$errors->get('arquivo')" class="mt-2" />
                    </div>

                    {{-- Banner fallback: só aparece quando PDF está selecionado --}}
                    <div id="banner-converter" class="hidden items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-amber-700 dark:text-amber-400 font-semibold flex-1">Leitura direta falhou? <span class="text-gray-400 font-normal">Use o conversor para gerar um XLSX e importe manualmente.</span></p>
                        <a href="{{ route('tenant.pdf-conversao.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow transition flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                            Converter PDF
                        </a>
                    </div>

                    <div class="flex justify-end pt-2 gap-4">
                        <a href="{{ route('tenant.importacoes') }}" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700">Cancelar</a>
                        <button type="button" id="btn-submit" onclick="submitForm()"
                            class="bg-slate-900 hover:bg-black text-white px-12 py-5 rounded-2xl shadow-2xl text-lg font-black tracking-widest uppercase transition">
                            <span id="btn-label">Iniciar Importacao</span>
                        </button>
                    </div>
                </div>

                <script>
                    let isPdf = false;

                    function onFileChange(input) {
                        const file = input.files[0];
                        if (!file) return;

                        isPdf = file.name.toLowerCase().endsWith('.pdf');

                        document.getElementById('file-name').textContent = file.name;
                        document.getElementById('file-name').classList.remove('hidden');
                        document.getElementById('file-name').className = 'text-xs font-bold mt-2 ' + (isPdf ? 'text-red-600' : 'text-blue-600');

                        document.getElementById('badge-pdf').classList.toggle('hidden', !isPdf);
                        document.getElementById('banner-converter').classList.toggle('hidden', !isPdf);
                        document.getElementById('banner-converter').classList.toggle('flex', isPdf);

                        document.getElementById('btn-label').textContent = isPdf ? 'Ler PDF Diretamente' : 'Iniciar Importacao';
                        document.getElementById('btn-submit').className = isPdf ?
                            'bg-blue-600 hover:bg-blue-700 text-white px-12 py-5 rounded-2xl shadow-2xl text-lg font-black tracking-widest uppercase transition' :
                            'bg-slate-900 hover:bg-black text-white px-12 py-5 rounded-2xl shadow-2xl text-lg font-black tracking-widest uppercase transition';
                    }

                    function submitForm() {
                        const fileInput = document.getElementById('arquivo');
                        if (!fileInput.files.length) {
                            alert('Selecione um arquivo.');
                            return;
                        }

                        const clienteId = document.getElementById('select-cliente').value;
                        if (!isPdf && !clienteId) {
                            alert('Selecione o cliente para o qual esta planilha pertence.');
                            return;
                        }

                        const dt = new DataTransfer();
                        dt.items.add(fileInput.files[0]);

                        if (isPdf) {
                            document.getElementById('arquivo-pdf').files = dt.files;
                            document.getElementById('form-pdf').submit();
                        } else {
                            document.getElementById('arquivo-xlsx').files = dt.files;
                            document.getElementById('form-xlsx').submit();
                        }
                    }
                </script>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow p-5 sm:p-8 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-widest text-slate-800 dark:text-white">Estrutura Esperada da Planilha</h3>
                        <p class="text-xs text-gray-400 mt-1">Cada linha representa um titulo. Informe o cabecalho exatamente conforme abaixo.</p>
                    </div>
                    <a href="{{ route('tenant.importacoes.template') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Baixar Modelo CSV
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-2 font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 rounded-l-xl">Coluna</th>
                                <th class="px-4 py-2 font-black uppercase tracking-widest text-gray-600 dark:text-gray-300">Obrigatorio</th>
                                <th class="px-4 py-2 font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 rounded-r-xl">Descricao / Exemplo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @php
                            $colunas = [
                            ['responsavel', true, 'Nome completo do responsavel financeiro', 'Joao da Silva'],
                            ['cpf', true, 'CPF do responsavel (com ou sem pontuacao)', '123.456.789-09'],
                            ['contato', true, 'Telefone/WhatsApp do responsavel', '(11) 91234-5678'],
                            ['email', false, 'E-mail do responsavel', 'joao@escola.com'],
                            ['rua', false, 'Logradouro do endereco', 'Rua das Flores'],
                            ['numero_end', false, 'Numero do endereco', '123'],
                            ['cep', false, 'CEP (apenas numeros)', '01310100'],
                            ['aluno', false, 'Nome do aluno/beneficiario', 'Maria da Silva'],
                            ['matricula', false, 'Matricula do aluno', 'MAT001'],
                            ['servico', false, 'Servico prestado / descricao do titulo', 'Mensalidade Escolar'],
                            ['numero_titulo', true, 'Numero unico do titulo', 'TIT-2024-001'],
                            ['vencimento', true, 'Data de vencimento (dd/mm/aaaa)', '31/12/2024'],
                            ['valor', true, 'Valor principal do titulo', '1500,00'],
                            ['multa', false, 'Percentual ou valor de multa', '2,00'],
                            ['juros', false, 'Percentual ou valor de juros', '1,00'],
                            ['honorarios', false, 'Honorarios advocaticios — se vazio, aplica regra de Configuracoes', '10,00'],
                            ];
                            @endphp
                            @foreach ($colunas as $coluna)
                            @php [$col, $req, $desc, $ex] = $coluna; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-2 font-mono font-bold text-blue-600 dark:text-blue-400">{{ $col }}</td>
                                <td class="px-4 py-2">
                                    @if ($req)
                                    <span class="bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 px-2 py-0.5 rounded-full font-black text-[10px] uppercase">Sim</span>
                                    @else
                                    <span class="bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-2 py-0.5 rounded-full font-black text-[10px] uppercase">Nao</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $desc }} <span class="italic text-gray-400">&mdash; ex: {{ $ex }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-4 text-xs text-gray-400"><strong>Dica:</strong> Cada linha = um titulo. Se um responsavel tiver multiplos titulos, repita os dados em cada linha. O sistema agrupa pelo CPF.</p>
            </div>
        </div>
    </div>
    <div id="modal-pdf" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="fecharModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18" />
                            </svg>
                        </div>
                    </div>
                    <span class="font-black text-sm uppercase tracking-widest text-slate-800 dark:text-white">Converter PDF em XLSX</span>
                </div>
                <button type="button" onclick="fecharModal()" class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-8 py-6">
                <form id="form-pdf" action="{{ route('tenant.pdf-conversao.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-5">
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-gray-600 transition-all">
                            <div class="flex flex-col items-center text-center px-4">
                                <svg class="w-8 h-8 mb-2 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><span class="text-red-500 font-black">Clique para selecionar</span> o PDF</p>
                                <p class="text-xs text-gray-400 mt-1">Apenas PDF &mdash; max. 20 MB</p>
                                <p id="pdf-name" class="text-xs text-red-600 font-bold mt-1 hidden"></p>
                            </div>
                            <input id="arquivo-pdf" name="arquivo" type="file" accept=".pdf" class="hidden" required onchange="document.getElementById('pdf-name').textContent = this.files[0]?.name; document.getElementById('pdf-name').classList.remove('hidden');" />
                        </label>
                        <div id="pdf-loading" class="hidden text-center py-2">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs font-black text-blue-600 dark:text-blue-300 uppercase tracking-widest">Convertendo... aguarde</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 text-center">O download do XLSX iniciara automaticamente apos a conversao.</p>
                        <div class="flex gap-3">
                            <button type="button" onclick="fecharModal()" class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancelar</button>
                            <button type="submit" id="btn-pdf" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Converter e Baixar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function fecharModal() {
            document.getElementById('modal-pdf').classList.add('hidden');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') fecharModal();
        });
        document.getElementById('form-pdf').addEventListener('submit', function() {
            document.getElementById('btn-pdf').disabled = true;
            document.getElementById('btn-pdf').classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('pdf-loading').classList.remove('hidden');
        });
    </script>
</x-app-layout>