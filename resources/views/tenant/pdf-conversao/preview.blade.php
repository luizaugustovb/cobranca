<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Preview — Tabelas Extraídas
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">
            {{ count($tables) }} tabela(s) encontrada(s) via <span class="text-blue-500">{{ $engine }}</span>
        </p>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
                <p class="text-sm text-emerald-700 font-bold">{{ session('success') }}</p>
            </div>
            @endif

            @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                @foreach ($errors->all() as $message)
                <p class="text-sm text-red-600 font-bold">{{ $message }}</p>
                @endforeach
            </div>
            @endif

            {{-- Ações --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('tenant.pdf-conversao.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-widest shadow transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>

                <form action="{{ route('tenant.pdf-conversao.importar') }}" method="POST" id="form-importar">
                    @csrf
                    <input type="hidden" name="key" value="{{ $key }}">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-blue-500/20 transition transform hover:scale-105 active:scale-95"
                        onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin w-4 h-4\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Importando...'; this.form.submit();">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Importar Dados
                    </button>
                </form>
            </div>

            {{-- Tabelas --}}
            @forelse ($tables as $index => $table)
            @php
            $rows = $table['rows'] ?? [];
            $headers = $table['headers'] ?? ($rows[0] ?? []);
            $data = isset($table['headers']) ? $rows : array_slice($rows, 1);
            @endphp

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center">
                            <span class="text-xs font-black text-blue-600">{{ $index + 1 }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-700 uppercase tracking-widest">Tabela {{ $index + 1 }}</p>
                            <p class="text-[9px] text-slate-400">{{ count($data) }} linha(s) · {{ count($headers) }} coluna(s)</p>
                        </div>
                    </div>
                    @if ($index === 0)
                    <span class="text-[9px] font-black uppercase tracking-widest bg-blue-100 text-blue-600 px-3 py-1 rounded-full">Principal</span>
                    @endif
                </div>

                @if (count($rows) === 0)
                <div class="p-10 text-center text-slate-400 text-sm">
                    Nenhuma linha encontrada nesta tabela.
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-gray-100">
                            <tr>
                                @foreach ($headers as $header)
                                <th class="px-4 py-3 font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">
                                    {{ $header ?? '—' }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($data as $row)
                            <tr class="hover:bg-blue-50/30 transition">
                                @foreach ($headers as $i => $header)
                                <td class="px-4 py-2.5 text-slate-600 whitespace-nowrap">
                                    {{ $row[$i] ?? '' }}
                                </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($headers) }}" class="px-4 py-6 text-center text-slate-400">
                                    Sem dados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-16 text-center">
                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-slate-400 font-bold text-sm">Nenhuma tabela encontrada no PDF.</p>
                <p class="text-slate-300 text-xs mt-1">Tente com o engine alternativo (pdfplumber ↔ tabula).</p>
                <a href="{{ route('tenant.pdf-conversao.create') }}"
                    class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest">
                    Tentar Novamente
                </a>
            </div>
            @endforelse

        </div>
    </div>
</x-app-layout>