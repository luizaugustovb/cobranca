<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <nav class="flex mb-4 text-xs font-bold text-gray-400 uppercase tracking-widest" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('tenant.acordos') }}" class="hover:text-blue-600 transition">Acordos</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-gray-600">Acordo #{{ $acordo->id }}</span>
                        </li>
                    </ol>
                </nav>
                <div class="flex items-center gap-4">
                    <h2 class="font-black text-2xl sm:text-4xl text-slate-900 dark:text-white tracking-tighter uppercase leading-none">
                        Acordo #{{ $acordo->id }}
                    </h2>
                    @php
                        $statusColor = match($acordo->status) {
                            'ativo'    => 'bg-emerald-100 text-emerald-700',
                            'quitado'  => 'bg-blue-100 text-blue-700',
                            'cancelado'=> 'bg-red-100 text-red-600',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs font-black uppercase rounded-full {{ $statusColor }} tracking-widest">
                        {{ ucfirst($acordo->status) }}
                    </span>
                </div>
                <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                    <span>Devedor: <a href="{{ route('tenant.devedores.show', $acordo->devedor) }}" class="font-bold text-blue-600 hover:underline">{{ $acordo->devedor->nome }}</a></span>
                    <span class="text-gray-300">�</span>
                    <span>Gerado em {{ $acordo->created_at->format('d/m/Y \�\s H:i') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-1">
                @if($acordo->asaas_link)
                    <a href="{{ $acordo->asaas_link }}" target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition shadow-lg shadow-blue-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Abrir Link
                    </a>
                @endif
                <a href="{{ route('tenant.devedores.show', $acordo->devedor) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Ver Devedor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl shadow-sm">
                    <p class="font-bold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ===== COLUNA PRINCIPAL (2/3) ===== --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Card Principal com Header Indigo --}}
                    @php
                        $pagas     = $acordo->acordoParcelas->where('status', 'pago')->count();
                        $total     = $acordo->acordoParcelas->count();
                        $progresso = $total > 0 ? round($pagas / $total * 100) : 0;
                        $valorParcela = $total > 0
                            ? ($acordo->valor_acordo - ($acordo->entrada ?? 0)) / $total
                            : 0;
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="bg-blue-600 px-8 py-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div>
                                <p class="text-xs font-black text-blue-200 uppercase tracking-widest mb-1">Valor do Acordo</p>
                                <p class="text-2xl sm:text-4xl font-black text-white tracking-tighter">R$ {{ number_format($acordo->valor_acordo, 2, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-blue-200 uppercase tracking-widest mb-1">Progresso</p>
                                <p class="text-3xl font-black text-white">{{ $pagas }}<span class="text-xl text-blue-300">/{{ $total }}</span></p>
                                <p class="text-xs text-blue-300 mt-0.5">parcelas pagas</p>
                            </div>
                        </div>
                        {{-- Barra de progresso --}}
                        <div class="h-2 bg-blue-100">
                            <div class="h-2 bg-emerald-500 transition-all duration-700" style="width: {{ $progresso }}%"></div>
                        </div>
                        {{-- Mini-stats --}}
                        <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
                            <div class="px-6 py-4 text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Valor Original</p>
                                <p class="text-sm font-black text-slate-700 dark:text-white">R$ {{ number_format($acordo->valor_original, 2, ',', '.') }}</p>
                            </div>
                            <div class="px-6 py-4 text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Desconto</p>
                                <p class="text-sm font-black text-emerald-600">- R$ {{ number_format($acordo->desconto, 2, ',', '.') }}</p>
                            </div>
                            <div class="px-6 py-4 text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Economia</p>
                                @php
                                    $pct = $acordo->valor_original > 0
                                        ? round($acordo->desconto / $acordo->valor_original * 100, 1)
                                        : 0;
                                @endphp
                                <p class="text-sm font-black text-emerald-600">{{ $pct }}%</p>
                            </div>
                        </div>
                    </div>

                    {{-- T�tulos Negociados --}}
                    @if($acordo->titulos->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-x-auto">
                        <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-tighter text-lg">T�tulos Negociados</h3>
                            <span class="ml-auto text-xs font-black text-gray-400 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/20 text-blue-500 px-3 py-1 rounded-full">
                                {{ $acordo->titulos->count() }} t�tulo(s)
                            </span>
                        </div>
        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">N� T�tulo</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Vencimento</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Valor Original</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Juros + Multa</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Honor�rios</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                @foreach($acordo->titulos->sortBy('vencimento') as $titulo)
                                    <tr class="bg-blue-50/30 dark:bg-blue-900/5 hover:bg-blue-50/60 transition-colors">
                                        <td class="px-6 py-4 text-sm font-black text-blue-700 dark:text-blue-400 uppercase tracking-tighter">
                                            #{{ $titulo->numero }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $titulo->vencimento->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-slate-700 dark:text-gray-200">
                                            R$ {{ number_format($titulo->valor_original, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-red-400 font-medium">
                                            @if(($titulo->juros + $titulo->multa) > 0)
                                                R$ {{ number_format($titulo->juros + $titulo->multa, 2, ',', '.') }}
                                            @else
                                                <span class="text-gray-300">�</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-amber-500 font-medium">
                                            @if($titulo->honorarios > 0)
                                                R$ {{ number_format($titulo->honorarios, 2, ',', '.') }}
                                            @else
                                                <span class="text-gray-300">�</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-slate-800 dark:text-white">
                                            R$ {{ number_format($titulo->valor_total, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- Linha de total --}}
                                <tr class="bg-blue-100/50 dark:bg-blue-900/20 font-black">
                                    <td colspan="5" class="px-6 py-3 text-right text-xs font-black uppercase tracking-widest text-blue-600">Total dos t�tulos</td>
                                    <td class="px-6 py-3 text-right text-sm font-black text-blue-700 dark:text-blue-300">
                                        R$ {{ number_format($acordo->titulos->sum(fn($t) => $t->valor_total), 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- Tabela de Parcelas --}}
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-x-auto">
                        <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-tighter text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Parcelas
                            </h3>
                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">{{ $pagas }}/{{ $total }} pagas</span>
                        </div>

                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">#</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Vencimento</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Valor</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-black uppercase tracking-widest text-gray-400">Gateway</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                @forelse($acordo->acordoParcelas->sortBy('numero') as $parcela)
                                    <tr class="{{ $parcela->status === 'pago' ? 'bg-emerald-50/40 dark:bg-emerald-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30' }} transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-black
                                                {{ $parcela->status === 'pago' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $parcela->numero }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $parcela->vencimento->format('d/m/Y') }}
                                            @if($parcela->vencimento->isPast() && $parcela->status === 'aberto')
                                                <span class="ml-1.5 text-[9px] bg-red-100 text-red-600 font-black uppercase px-1.5 py-0.5 rounded-full tracking-wide">Vencida</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-slate-800 dark:text-white">
                                            R$ {{ number_format($parcela->valor, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-xs font-black uppercase rounded-full
                                                {{ $parcela->status === 'pago'   ? 'bg-emerald-100 text-emerald-700' :
                                                  ($parcela->status === 'aberto' ? 'bg-amber-100 text-amber-700'    : 'bg-red-100 text-red-600') }}">
                                                {{ ucfirst($parcela->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($parcela->payment_id)
                                                <span class="text-[10px] text-blue-500 font-mono bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded truncate max-w-[120px] block">{{ $parcela->payment_id }}</span>
                                            @else
                                                <span class="text-gray-300 text-sm">�</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic text-sm">Nenhuma parcela gerada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagamentos Recebidos --}}
                    @if($acordo->pagamentos->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-x-auto">
                        <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-tighter text-lg">Pagamentos Recebidos</h3>
                        </div>
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Data</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Valor</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Forma</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Gateway ID</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @foreach($acordo->pagamentos as $pgto)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($pgto->data_pagamento)->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-emerald-600">R$ {{ number_format($pgto->valor, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 uppercase">{{ $pgto->forma_pagamento ?? '�' }}</td>
                                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">{{ $pgto->gateway_id ?? '�' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>

                {{-- ===== SIDEBAR (1/3) ===== --}}
                <div class="space-y-6">

                    {{-- Card: Forma de Pagamento --}}
                    @php
                        $formaConfig = match($acordo->forma_pagamento) {
                            'BOLETO'      => ['icon' => '??', 'label' => 'Boleto Banc�rio',    'color' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200'],
                            'PIX'         => ['icon' => '?', 'label' => 'Pix',                'color' => 'bg-green-50 dark:bg-green-900/20 border-green-200'],
                            'CREDIT_CARD' => ['icon' => '??', 'label' => 'Cart�o de Cr�dito',  'color' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200'],
                            default       => ['icon' => '??', 'label' => 'Livre (devedor escolhe)', 'color' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200'],
                        };
                    @endphp
                    <div class="rounded-2xl p-5 border-2 {{ $formaConfig['color'] }}">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Forma de Pagamento</p>
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ $formaConfig['icon'] }}</span>
                            <p class="font-black text-slate-800 dark:text-white text-base">{{ $formaConfig['label'] }}</p>
                        </div>
                    </div>

                    {{-- Card: Resumo Financeiro --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Resumo Financeiro</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <span class="text-gray-500">D�vida original</span>
                                <span class="font-bold text-slate-700 dark:text-white">R$ {{ number_format($acordo->valor_original, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 text-emerald-600">
                                <span class="font-medium">Desconto concedido</span>
                                <span class="font-black">- R$ {{ number_format($acordo->desconto, 2, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <span class="font-black text-slate-700 dark:text-white uppercase text-xs tracking-widest">Valor do Acordo</span>
                                <span class="font-black text-blue-600 text-base">R$ {{ number_format($acordo->valor_acordo, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card: Condi��es --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Condi��es</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <span class="text-gray-500">Entrada</span>
                                <span class="font-bold text-slate-700 dark:text-white">
                                    {{ $acordo->entrada > 0 ? 'R$ ' . number_format($acordo->entrada, 2, ',', '.') : 'Sem entrada' }}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <span class="text-gray-500">Parcelas</span>
                                <span class="font-bold text-slate-700 dark:text-white">{{ $total }}x de R$ {{ number_format($valorParcela, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <span class="text-gray-500">Pagas</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-1.5 bg-emerald-500 rounded-full" style="width: {{ $progresso }}%"></div>
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-white text-xs">{{ $progresso }}%</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <span class="text-gray-500">Restante</span>
                                @php
                                    $valorRestante = $acordo->acordoParcelas->where('status', '!=', 'pago')->sum('valor');
                                @endphp
                                <span class="font-bold {{ $valorRestante > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                    R$ {{ number_format($valorRestante, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Card: Asaas Link --}}
                    @if($acordo->asaas_link)
                    <div class="bg-blue-600 rounded-2xl p-6 shadow-xl shadow-blue-500/30">
                        <p class="text-xs font-black text-blue-200 uppercase tracking-widest mb-3">Link de Pagamento</p>
                        <p class="text-xs text-blue-300 break-all mb-4 leading-relaxed">{{ $acordo->asaas_link }}</p>
                        <a href="{{ $acordo->asaas_link }}" target="_blank"
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white/20 hover:bg-white/30 text-white text-xs font-black uppercase tracking-widest rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Abrir Portal de Pagamento
                        </a>
                    </div>
                    @endif

                    {{-- Acesso r�pido ao devedor --}}
                    <a href="{{ route('tenant.devedores.show', $acordo->devedor) }}"
                       class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-5 hover:border-blue-300 hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 group-hover:bg-blue-200 transition">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Devedor</p>
                            <p class="font-black text-slate-800 dark:text-white text-sm truncate">{{ $acordo->devedor->nome }}</p>
                            @if($acordo->devedor->cpf_cnpj)
                                <p class="text-xs text-gray-400 font-mono">{{ $acordo->devedor->cpf_cnpj }}</p>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 ml-auto transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>