<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
            Preview — Importação Activesoft
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">
            Revise e edite os dados antes de confirmar. Linhas desmarcadas serão ignoradas.
        </p>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-full mx-auto sm:px-4 lg:px-6">

            @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl">
                <p class="font-bold mb-1">Erros:</p>
                <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('tenant.importacoes.activesoft.confirmar') }}" method="POST" id="form-confirmar">
                @csrf
                <input type="hidden" name="key" value="{{ $key }}">

                {{-- Barra de ação fixa --}}
                <div class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between mb-6 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="toggleAll(true)"
                            class="text-xs font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 px-3 py-1 border border-blue-200 rounded-lg">
                            Marcar todos
                        </button>
                        <button type="button" onclick="toggleAll(false)"
                            class="text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700 px-3 py-1 border border-gray-200 rounded-lg">
                            Desmarcar todos
                        </button>
                        <span id="count-badge" class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full"></span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('tenant.importacoes.activesoft') }}"
                            class="px-6 py-2 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-xl shadow text-xs font-black tracking-widest uppercase transition"
                            onclick="this.disabled=true; this.textContent='Importando...';">
                            Confirmar Importação
                        </button>
                    </div>
                </div>

                @php $itemGlobal = 0; @endphp

                @foreach($responsaveis as $ri => $resp)
                @if(!empty($resp['itens']))

                {{-- Card do Responsável --}}
                <div class="mb-8 bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- Cabeçalho do responsável (editável) --}}
                    <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Responsável</p>
                                <p class="font-black text-slate-800 dark:text-white text-lg leading-none">{{ $resp['nome'] }}</p>
                                <p class="text-sm text-gray-500 mt-1 font-mono">{{ $resp['cpf'] }} &nbsp;·&nbsp; {{ $resp['telefone'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ implode(', ', array_filter([$resp['rua'], $resp['numero'], $resp['bairro'], $resp['cidade'], $resp['estado'], $resp['cep']])) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-xs font-black bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                                    {{ count($resp['itens']) }} parcela(s)
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Tabela de itens --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                    <th class="px-3 py-2 text-center w-8">
                                        <input type="checkbox" checked class="accent-blue-600 cursor-pointer"
                                            onchange="toggleGroup(this, {{ $ri }})">
                                    </th>
                                    <th class="px-3 py-2 text-left font-black uppercase tracking-widest">Nº Título / Parcela</th>
                                    <th class="px-3 py-2 text-left font-black uppercase tracking-widest">Situação</th>
                                    <th class="px-3 py-2 text-left font-black uppercase tracking-widest">Vencimento</th>
                                    <th class="px-3 py-2 text-left font-black uppercase tracking-widest min-w-[180px]">Serviço</th>
                                    <th class="px-3 py-2 text-left font-black uppercase tracking-widest min-w-[140px]">Aluno</th>
                                    <th class="px-3 py-2 text-right font-black uppercase tracking-widest">Valor</th>
                                    <th class="px-3 py-2 text-right font-black uppercase tracking-widest">Multa/Juros</th>
                                    <th class="px-3 py-2 text-right font-black uppercase tracking-widest">A Receber</th>
                                    {{-- Campos ocultos do responsável --}}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($resp['itens'] as $ii => $item)
                                @php $idx = $itemGlobal++; @endphp
                                <tr class="hover:bg-blue-50 dark:hover:bg-gray-750 item-row" data-group="{{ $ri }}">
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox"
                                            name="itens[{{ $idx }}][importar]"
                                            value="1"
                                            checked
                                            class="accent-blue-600 cursor-pointer item-check"
                                            data-group="{{ $ri }}"
                                            onchange="updateCount()">
                                    </td>
                                    <td class="px-3 py-2">
                                        {{-- Campos editáveis ocultos para dados do responsável --}}
                                        <input type="hidden" name="itens[{{ $idx }}][nome]" value="{{ $resp['nome'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][cpf]" value="{{ $resp['cpf'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][telefone]" value="{{ $resp['telefone'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][rua]" value="{{ $resp['rua'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][numero]" value="{{ $resp['numero'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][bairro]" value="{{ $resp['bairro'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][cidade]" value="{{ $resp['cidade'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][estado]" value="{{ $resp['estado'] }}">
                                        <input type="hidden" name="itens[{{ $idx }}][cep]" value="{{ $resp['cep'] }}">

                                        <div class="flex gap-1 items-center">
                                            <input type="text"
                                                name="itens[{{ $idx }}][numero_titulo]"
                                                value="{{ $item['numero_titulo'] }}"
                                                class="w-20 rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs font-mono font-bold py-1 px-2 focus:ring-blue-500"
                                                title="Nº Título">
                                            <span class="text-gray-300">/</span>
                                            <input type="text"
                                                name="itens[{{ $idx }}][parcela]"
                                                value="{{ $item['parcela'] }}"
                                                class="w-14 rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs font-mono py-1 px-2 focus:ring-blue-500"
                                                title="Parcela">
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <select name="itens[{{ $idx }}][situacao]"
                                            class="rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs py-1 px-2 focus:ring-blue-500">
                                            <option value="Em aberto" {{ strtolower($item['situacao']) === 'em aberto' ? 'selected' : '' }}>Em aberto</option>
                                            <option value="Pago" {{ strtolower($item['situacao']) !== 'em aberto' ? 'selected' : '' }}>Pago</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text"
                                            name="itens[{{ $idx }}][vencimento]"
                                            value="{{ $item['vencimento'] }}"
                                            class="w-28 rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs font-mono py-1 px-2 focus:ring-blue-500"
                                            placeholder="DD/MM/AAAA">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text"
                                            name="itens[{{ $idx }}][servico]"
                                            value="{{ $item['servico'] }}"
                                            class="w-full min-w-[160px] rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs py-1 px-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text"
                                            name="itens[{{ $idx }}][aluno]"
                                            value="{{ $item['aluno'] }}"
                                            class="w-full min-w-[130px] rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs py-1 px-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <input type="text"
                                            name="itens[{{ $idx }}][valor_servico]"
                                            value="{{ $item['valor_servico'] }}"
                                            class="w-24 rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs font-mono text-right py-1 px-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <input type="text"
                                            name="itens[{{ $idx }}][multa_juros]"
                                            value="{{ $item['multa_juros'] }}"
                                            class="w-20 rounded border-gray-200 bg-gray-50 dark:bg-gray-700 text-xs font-mono text-right py-1 px-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <span class="font-black text-slate-700 dark:text-slate-300">
                                            R$ {{ $item['valor_receber'] !== '0,00' ? $item['valor_receber'] : $item['valor_servico'] }}
                                        </span>
                                        <input type="hidden" name="itens[{{ $idx }}][valor_receber]" value="{{ $item['valor_receber'] }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @endif
                @endforeach

                {{-- Rodapé de confirmação --}}
                <div class="flex justify-end gap-4 mt-6 pb-6">
                    <a href="{{ route('tenant.importacoes.activesoft') }}"
                        class="px-8 py-4 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-4 rounded-2xl shadow-xl text-sm font-black tracking-widest uppercase transition"
                        onclick="this.disabled=true; this.textContent='Importando...';">
                        Confirmar Importação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateCount() {
            const total = document.querySelectorAll('.item-check').length;
            const checked = document.querySelectorAll('.item-check:checked').length;
            document.getElementById('count-badge').textContent = checked + ' / ' + total + ' selecionados';
        }

        function toggleAll(state) {
            document.querySelectorAll('.item-check').forEach(cb => cb.checked = state);
            updateCount();
        }

        function toggleGroup(masterCb, group) {
            document.querySelectorAll('.item-check[data-group="' + group + '"]').forEach(cb => {
                cb.checked = masterCb.checked;
            });
            updateCount();
        }

        // Inicializa contagem
        updateCount();
    </script>
</x-app-layout>