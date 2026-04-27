<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <nav class="flex mb-3 text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <a href="{{ route('tenant.clientes') }}" class="hover:text-blue-600">Clientes</a>
                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-600">Relatorio de Pagamentos</span>
                </nav>
                <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white tracking-tighter uppercase">
                    Relatorio: {{ $cliente->nome }}
                </h2>
                <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Pagamentos recebidos no periodo selecionado</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                @php
                $telCliente = preg_replace('/[^0-9]/', '', $cliente->telefone ?? '');
                $telWaCliente = strlen($telCliente) >= 10 ? '55' . $telCliente : '';
                $msgResumo = urlencode(
                "Prezado(a) " . explode(' ', $cliente->nome)[0] . ", segue o relatorio de pagamentos do periodo " .
                \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') . " a " .
                \Carbon\Carbon::parse($dataFim)->format('d/m/Y') . ". " .
                "Total recebido: R$ " . number_format($totalGeral, 2, ',', '.') . ". " .
                "Acesse o relatorio completo em: " . request()->fullUrl()
                );
                @endphp
                @if($telWaCliente)
                <a href="https://wa.me/{{ $telWaCliente }}?text={{ $msgResumo }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-xl font-bold text-sm uppercase transition hover:bg-green-600 shadow-lg shadow-green-500/20 gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.183-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.765-5.771zm3.392 8.244c-.144.405-.837.774-1.171.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.512-2.961-2.628-.086-.117-.704-.933-.704-1.782 0-.85.433-1.268.587-1.442.155-.174.337-.217.45-.217l.323.004c.103.005.23.02.361.33.136.323.466 1.137.507 1.219.04.083.067.18.013.287-.054.107-.081.174-.162.27-.081.094-.17.21-.242.282-.081.082-.166.171-.072.332.094.162.418.689.897 1.115.617.551 1.137.721 1.3.8.163.078.261.066.359-.045.099-.112.424-.492.537-.66.113-.168.225-.141.38-.084.155.057.986.465 1.155.549.169.085.281.127.322.197.041.07.041.405-.103.81z" />
                    </svg>
                    Enviar Resumo
                </a>
                @endif
                <a href="{{ route('tenant.clientes.relatorio.pdf', [$cliente, 'data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-xl font-bold text-sm uppercase transition hover:bg-red-700 shadow-lg gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Imprimir / PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filtro de periodo --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 p-6">
                <form method="GET" action="{{ route('tenant.clientes.relatorio', $cliente) }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Data Inicio</label>
                        <input type="date" name="data_inicio" value="{{ $dataInicio }}"
                            class="rounded-xl border-gray-200 bg-gray-50 text-sm font-medium py-2 px-3 dark:bg-gray-700" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Data Fim</label>
                        <input type="date" name="data_fim" value="{{ $dataFim }}"
                            class="rounded-xl border-gray-200 bg-gray-50 text-sm font-medium py-2 px-3 dark:bg-gray-700" />
                    </div>
                    <button type="submit"
                        class="px-5 py-2 bg-slate-900 text-white rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-black transition">
                        Filtrar
                    </button>
                </form>
            </div>

            {{-- Cards de resumo --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Total Recebido</p>
                    <p class="text-2xl font-black text-emerald-600">R$ {{ number_format($totalGeral, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Pagamentos</p>
                    <p class="text-2xl font-black text-blue-600">{{ $pagamentos->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">transacoes no periodo</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Devedores</p>
                    <p class="text-2xl font-black text-slate-700 dark:text-white">{{ $porDevedor->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">que realizaram pagamento</p>
                </div>
            </div>

            {{-- Tabela por devedor --}}
            @forelse($porDevedor as $item)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                    <div>
                        <p class="font-black text-slate-800 dark:text-white uppercase tracking-tighter">
                            {{ $item['devedor']->nome ?? 'Devedor removido' }}
                        </p>
                        @if($item['devedor'])
                        <p class="text-xs text-gray-400 font-bold">{{ $item['devedor']->cpf_cnpj }}</p>
                        @endif
                    </div>
                    <span class="text-sm font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                        R$ {{ number_format($item['total'], 2, ',', '.') }}
                    </span>
                </div>
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/30">
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Acordo</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Forma</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($item['pagamentos'] as $pag)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition">
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $pag->data_pagamento->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3">
                                <a href="{{ route('tenant.acordos.show', $pag->acordo_id) }}"
                                    class="text-xs font-bold text-blue-500 hover:underline">
                                    Acordo #{{ $pag->acordo_id }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-400 uppercase font-bold">
                                {{ $pag->forma_pagamento ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-black text-emerald-600">
                                R$ {{ number_format($pag->valor, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow border border-gray-100 dark:border-gray-700 p-16 text-center">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm font-bold uppercase tracking-widest text-gray-400">Nenhum pagamento encontrado neste periodo.</p>
            </div>
            @endforelse

        </div>
    </div>
</x-app-layout>