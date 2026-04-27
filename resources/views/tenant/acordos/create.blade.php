<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Simulador de Acordo
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Refinanciamento de dívida para: <strong class="text-blue-600">{{ $devedor->nome }}</strong></p>
    </x-slot>

    <div class="py-6 sm:py-12" x-data="{
        principal:      {{ (float) $totalPrincipal }},
        jurosOrig:      {{ (float) $totalJuros }},
        multaOrig:      {{ (float) $totalMulta }},
        honorarios:     {{ (float) $totalHonorarios }},
        jurosNeg:       {{ (float) $totalJuros }},
        multaNeg:       {{ (float) $totalMulta }},
        entrada:        0,
        parcelas:       1,
        formaPagamento: 'UNDEFINED',
        get descontoTotal() {
            return (this.jurosOrig - Math.min(Math.max(0, this.jurosNeg), this.jurosOrig))
                 + (this.multaOrig - Math.min(Math.max(0, this.multaNeg), this.multaOrig));
        },
        get valorAcordo() {
            return this.principal
                 + Math.min(Math.max(0, this.jurosNeg), this.jurosOrig)
                 + Math.min(Math.max(0, this.multaNeg), this.multaOrig)
                 + this.honorarios;
        },
        get valorParcela() {
            return Math.max(0, this.valorAcordo - this.entrada) / this.parcelas;
        },
        fmt(v) { return v.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Calculadora -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-5 sm:p-10 border border-gray-100 dark:border-gray-700">
                        <form action="{{ route('tenant.acordos.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="devedor_id" value="{{ $devedor->id }}">
                            <input type="hidden" name="valor_original" :value="principal + jurosOrig + multaOrig + honorarios">
                            <input type="hidden" name="desconto" :value="descontoTotal">
                            <input type="hidden" name="valor_acordo" :value="valorAcordo">
                            <input type="hidden" name="forma_pagamento" :value="formaPagamento">

                            <div class="space-y-8">

                                {{-- Breakdown dos valores --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Composição da Dívida</p>
                                    <div class="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-700/40">
                                                <tr>
                                                    <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Componente</th>
                                                    <th class="px-5 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Valor Original</th>
                                                    <th class="px-5 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Negociado</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                                <tr class="bg-white dark:bg-gray-800">
                                                    <td class="px-5 py-4 font-bold text-slate-700 dark:text-gray-200">Principal</td>
                                                    <td class="px-5 py-4 text-right font-black text-slate-800 dark:text-white">R$ {{ number_format($totalPrincipal, 2, ',', '.') }}</td>
                                                    <td class="px-5 py-4 text-right">
                                                        <span class="text-sm font-black text-slate-500 italic">R$ {{ number_format($totalPrincipal, 2, ',', '.') }}</span>
                                                    </td>
                                                </tr>
                                                <tr class="bg-white dark:bg-gray-800">
                                                    <td class="px-5 py-4 font-bold text-slate-700 dark:text-gray-200">Juros</td>
                                                    <td class="px-5 py-4 text-right font-black text-slate-800 dark:text-white">R$ {{ number_format($totalJuros, 2, ',', '.') }}</td>
                                                    <td class="px-5 py-4 text-right">
                                                        @if($totalJuros > 0)
                                                        <input type="number" step="0.01" min="0" max="{{ $totalJuros }}"
                                                            x-model.number="jurosNeg"
                                                            class="w-32 text-right text-sm font-black rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 focus:ring-blue-500 focus:border-blue-500" />
                                                        @else
                                                        <span class="text-sm text-gray-400 italic">R$ 0,00</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr class="bg-white dark:bg-gray-800">
                                                    <td class="px-5 py-4 font-bold text-slate-700 dark:text-gray-200">Multa</td>
                                                    <td class="px-5 py-4 text-right font-black text-slate-800 dark:text-white">R$ {{ number_format($totalMulta, 2, ',', '.') }}</td>
                                                    <td class="px-5 py-4 text-right">
                                                        @if($totalMulta > 0)
                                                        <input type="number" step="0.01" min="0" max="{{ $totalMulta }}"
                                                            x-model.number="multaNeg"
                                                            class="w-32 text-right text-sm font-black rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600 py-2 px-3 focus:ring-blue-500 focus:border-blue-500" />
                                                        @else
                                                        <span class="text-sm text-gray-400 italic">R$ 0,00</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr class="bg-amber-50 dark:bg-amber-900/20">
                                                    <td class="px-5 py-4 font-bold text-amber-700 dark:text-amber-400">
                                                        Honorários
                                                        <span class="ml-2 text-[9px] bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300 font-black uppercase px-2 py-0.5 rounded-full tracking-widest">Fixo</span>
                                                    </td>
                                                    <td class="px-5 py-4 text-right font-black text-amber-700 dark:text-amber-400">R$ {{ number_format($totalHonorarios, 2, ',', '.') }}</td>
                                                    <td class="px-5 py-4 text-right">
                                                        <span class="text-sm font-black text-amber-600 dark:text-amber-400">R$ {{ number_format($totalHonorarios, 2, ',', '.') }}</span>
                                                    </td>
                                                </tr>
                                                <tr class="bg-blue-50 dark:bg-blue-900/20">
                                                    <td class="px-5 py-4 font-black text-blue-700 dark:text-blue-300 uppercase text-xs tracking-widest" colspan="2">Total Negociado</td>
                                                    <td class="px-5 py-4 text-right font-black text-blue-700 dark:text-blue-300 text-lg">
                                                        R$ <span x-text="fmt(valorAcordo)"></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div x-show="descontoTotal > 0" class="mt-3 text-right">
                                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">
                                            Desconto concedido: R$ <span x-text="fmt(descontoTotal)"></span>
                                        </span>
                                    </div>
                                </div>

                                <hr class="border-gray-50 dark:border-gray-700">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <x-input-label for="entrada" :value="__('Valor da Entrada (R$)')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2" />
                                        <x-text-input id="entrada" name="entrada" type="number" step="0.01" x-model.number="entrada" class="w-full text-xl font-bold rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50" />
                                    </div>
                                    <div>
                                        <x-input-label for="parcelas" :value="__('Quantidade de Parcelas')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2" />
                                        <select id="parcelas" name="parcelas" x-model.number="parcelas" class="w-full text-xl font-bold rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50">
                                            @for($i=1; $i<=24; $i++) <option value="{{$i}}">{{$i}}x parcelas</option> @endfor
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="vencimento_primeira" :value="__('Data da Primeira Parcela')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2" />
                                    <x-text-input id="vencimento_primeira" name="vencimento_primeira" type="date" class="w-full text-xl font-bold rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50" value="{{ date('Y-m-d') }}" required />
                                </div>

                                <div class="bg-blue-600 rounded-3xl p-5 sm:p-10 shadow-2xl shadow-blue-500/30 transform hover:scale-[1.02] transition">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 text-white">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest opacity-70">Resultado da Simulação</p>
                                            <p class="text-5xl font-black tracking-tighter mt-2"><span x-text="parcelas"></span>x R$ <span x-text="fmt(valorParcela)"></span></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-black uppercase tracking-widest opacity-70">Valor Final</p>
                                            <p class="text-2xl font-black">R$ <span x-text="fmt(valorAcordo)"></span></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Forma de Pagamento --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Forma de Pagamento</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        {{-- Boleto --}}
                                        <button type="button" @click="formaPagamento = 'BOLETO'"
                                            :class="formaPagamento === 'BOLETO' ? 'ring-2 ring-blue-600 bg-blue-50 dark:bg-blue-900/30 border-blue-300' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'"
                                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition text-center">
                                            <svg class="w-7 h-7 text-slate-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-gray-200">Boleto</span>
                                        </button>
                                        {{-- PIX --}}
                                        <button type="button" @click="formaPagamento = 'PIX'"
                                            :class="formaPagamento === 'PIX' ? 'ring-2 ring-blue-600 bg-blue-50 dark:bg-blue-900/30 border-blue-300' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'"
                                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition text-center">
                                            <svg class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-gray-200">PIX</span>
                                        </button>
                                        {{-- Cartao --}}
                                        <button type="button" @click="formaPagamento = 'CREDIT_CARD'"
                                            :class="formaPagamento === 'CREDIT_CARD' ? 'ring-2 ring-blue-600 bg-blue-50 dark:bg-blue-900/30 border-blue-300' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'"
                                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition text-center">
                                            <svg class="w-7 h-7 text-slate-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-gray-200">Cartao</span>
                                        </button>
                                        {{-- Livre --}}
                                        <button type="button" @click="formaPagamento = 'UNDEFINED'"
                                            :class="formaPagamento === 'UNDEFINED' ? 'ring-2 ring-blue-600 bg-blue-50 dark:bg-blue-900/30 border-blue-300' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'"
                                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition text-center">
                                            <svg class="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-gray-200">Livre</span>
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2 font-medium">
                                        <span x-show="formaPagamento === 'UNDEFINED'">O devedor poderá escolher a forma ao pagar pelo link Asaas.</span>
                                        <span x-show="formaPagamento !== 'UNDEFINED'" x-text="'As cobranças serão geradas no Asaas na modalidade: ' + formaPagamento"></span>
                                    </p>
                                </div>

                                <div class="pt-2">
                                    <x-primary-button class="w-full bg-slate-900 hover:bg-black px-12 py-6 rounded-2xl shadow-2xl text-xl font-black tracking-widest uppercase items-center justify-center">
                                        <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Gerar Cobrança
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Resumo Títulos Direita -->
                <div class="space-y-8">
                    <div class="bg-gray-900 rounded-3xl p-5 sm:p-8 shadow-xl text-white">
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-500 mb-6">Títulos Selecionados</h3>
                        <ul class="space-y-5">
                            @foreach($devedor->titulos as $titulo)
                            <li class="border-b border-gray-700 pb-4 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-xs font-bold uppercase">{{ $titulo->numero }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $titulo->vencimento->format('d/m/Y') }}</p>
                                    </div>
                                    <p class="font-black text-emerald-400 text-sm">R$ {{ number_format($titulo->valor_total, 2, ',', '.') }}</p>
                                </div>
                                <div class="text-[10px] text-gray-500 space-y-0.5">
                                    <div class="flex justify-between"><span>Principal</span><span>R$ {{ number_format($titulo->valor_original, 2, ',', '.') }}</span></div>
                                    @if($titulo->juros > 0)<div class="flex justify-between"><span>Juros</span><span>R$ {{ number_format($titulo->juros, 2, ',', '.') }}</span></div>@endif
                                    @if($titulo->multa > 0)<div class="flex justify-between"><span>Multa</span><span>R$ {{ number_format($titulo->multa, 2, ',', '.') }}</span></div>@endif
                                    @if($titulo->honorarios > 0)<div class="flex justify-between text-amber-400"><span>Honorários</span><span>R$ {{ number_format($titulo->honorarios, 2, ',', '.') }}</span></div>@endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        {{-- Total negociado reativo --}}
                        <div class="mt-5 pt-4 border-t border-gray-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Total Negociado</span>
                            <span class="font-black text-emerald-400 text-lg">R$ <span x-text="fmt(valorAcordo)"></span></span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-8 shadow-xl border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black uppercase tracking-widest text-blue-500 mb-4">Informação importante</h3>
                        <p class="text-sm text-gray-500 font-medium">Ao confirmar este acordo, todos os títulos listados acima serão cancelados e substituídos pelas novas parcelas geradas aqui.</p>
                        <p class="text-[10px] text-amber-600 font-bold mt-3">? Honorários não podem ser reduzidos na negociação.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>