<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
            Simulador de Acordo
        </h2>
        <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">Refinanciamento de dívida para: <strong class="text-indigo-600">{{ $devedor->nome }}</strong></p>
    </x-slot>

    <div class="py-12" x-data="{
        original: {{ $totalOriginal }},
        desconto: 0,
        entrada: 0,
        parcelas: 1,
        get valorAcordo() { return Math.max(0, this.original - this.desconto); },
        get valorParcela() { return (this.valorAcordo - this.entrada) / this.parcelas; }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Calculadora -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-10 border border-gray-100 dark:border-gray-700">
                        <form action="{{ route('tenant.acordos.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="devedor_id" value="{{ $devedor->id }}">
                            <input type="hidden" name="valor_original" :value="original">
                            <input type="hidden" name="valor_acordo" :value="valorAcordo">

                            <div class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-600">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Valor Original em Débito</p>
                                        <p class="text-3xl font-black text-slate-900 dark:text-white">R$ {{ number_format($totalOriginal, 2, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <x-input-label for="desconto" :value="__('Conceder Desconto (R$)')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2"/>
                                        <x-text-input id="desconto" name="desconto" type="number" step="0.01" x-model.number="desconto" class="w-full text-2xl font-black text-red-500 rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50 pr-10" />
                                        <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase">Novo Valor: <span class="text-indigo-600">R$ <span x-text="valorAcordo.toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span></span></p>
                                    </div>
                                </div>

                                <hr class="border-gray-50 dark:border-gray-700">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <x-input-label for="entrada" :value="__('Valor da Entrada (R$)')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2"/>
                                        <x-text-input id="entrada" name="entrada" type="number" step="0.01" x-model.number="entrada" class="w-full text-xl font-bold rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50" />
                                    </div>
                                    <div>
                                        <x-input-label for="parcelas" :value="__('Quantidade de Parcelas')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2"/>
                                        <select id="parcelas" name="parcelas" x-model.number="parcelas" class="w-full text-xl font-bold rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50">
                                            @for($i=1; $i<=24; $i++) <option value="{{$i}}">{{$i}}x parcelas</option> @endfor
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <x-input-label for="vencimento_primeira" :value="__('Data da Primeira Parcela')" class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-gray-300 mb-2"/>
                                    <x-text-input id="vencimento_primeira" name="vencimento_primeira" type="date" class="w-full text-xl font-bold rounded-2xl border-gray-100 dark:bg-gray-700 py-4 bg-gray-50" value="{{ date('Y-m-d') }}" required />
                                </div>

                                <div class="bg-indigo-600 rounded-3xl p-10 shadow-2xl shadow-indigo-500/30 transform hover:scale-[1.02] transition">
                                    <div class="flex justify-between items-center text-white">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest opacity-70">Resultado da Simulação</p>
                                            <p class="text-5xl font-black tracking-tighter mt-2"><span x-text="parcelas"></span>x R$ <span x-text="valorParcela.toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span></p>
                                        </div>
                                        <div class="text-right">
                                             <p class="text-xs font-black uppercase tracking-widest opacity-70">Valor Final</p>
                                             <p class="text-2xl font-black">R$ <span x-text="valorAcordo.toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6">
                                    <x-primary-button class="w-full bg-slate-900 hover:bg-black px-12 py-6 rounded-2xl shadow-2xl text-xl font-black tracking-widest uppercase items-center justify-center">
                                        <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Confirmar e Gerar Boletos
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Resumo Títulos Direita -->
                <div class="space-y-8">
                    <div class="bg-gray-900 rounded-3xl p-8 shadow-xl text-white">
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-500 mb-6">Títulos Selecionados</h3>
                        <ul class="space-y-4">
                            @foreach($devedor->titulos as $titulo)
                                <li class="flex justify-between items-center">
                                    <div>
                                        <p class="text-xs font-bold uppercase">{{ $titulo->numero }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $titulo->vencimento->format('d/m/Y') }}</p>
                                    </div>
                                    <p class="font-black text-emerald-400">R$ {{ number_format($titulo->valor_original, 2, ',', '.') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black uppercase tracking-widest text-indigo-500 mb-4">Informação importante</h3>
                        <p class="text-sm text-gray-500 font-medium">Ao confirmar este acordo, todos os títulos listados acima serão cancelados e substituídos pelas novas parcelas geradas aqui.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
