<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 tracking-tighter uppercase flex items-center">
                <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                Gerar Novo Título
            </h2>
            <a href="{{ route('tenant.titulos') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12" x-data="calculadoraTitulo()">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Formulário (2/3) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-2xl rounded-3xl border border-slate-100 overflow-hidden">
                        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Preencha os dados do título de cobrança</p>
                        </div>

                        <form method="POST" action="{{ route('tenant.titulos.store') }}" class="p-8 space-y-6">
                            @csrf

                            {{-- Devedor --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Devedor *</label>
                                <select name="devedor_id" x-model="devedorId" @change="onDevedorChange()" required
                                    class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('devedor_id') border-red-400 @enderror">
                                    <option value="">— Selecione o devedor —</option>
                                    @foreach($devedores as $devedor)
                                    <option value="{{ $devedor->id }}" {{ old('devedor_id') == $devedor->id ? 'selected' : '' }}>
                                        {{ $devedor->nome }} — {{ $devedor->cpf_cnpj }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('devedor_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                <p x-show="taxas.cliente_nome" class="mt-1.5 text-xs text-blue-600 font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>
                                    Carteira: <span x-text="taxas.cliente_nome"></span>
                                </p>
                            </div>

                            {{-- Número do Título --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Número do Título *</label>
                                <input type="text" name="numero" value="{{ old('numero') }}" required placeholder="Ex: 0001/2026"
                                    class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('numero') border-red-400 @enderror">
                                @error('numero')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Valor e Vencimento --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Valor Original (R$) *</label>
                                    <input type="number" name="valor_original" x-model="valorOriginal" @input="calcular()" required min="0.01" step="0.01" placeholder="0,00"
                                        class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('valor_original') border-red-400 @enderror">
                                    @error('valor_original')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Vencimento *</label>
                                    <input type="date" name="vencimento" x-model="vencimento" @change="onVencimentoChange()" required
                                        class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('vencimento') border-red-400 @enderror">
                                    @error('vencimento')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Encargos (calculados automaticamente, editáveis) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                        Multa (R$)
                                        <span x-show="taxas.multa_percentual > 0" x-text="'— ' + pct(taxas.multa_percentual)" class="text-amber-500 normal-case ml-1"></span>
                                    </label>
                                    <input type="number" name="multa" x-model="multa" min="0" step="0.01" placeholder="0,00"
                                        class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                    <p x-show="!vencido && vencimento" class="mt-1 text-[10px] text-slate-400">Não vencido — multa zerada</p>
                                    <p x-show="vencido" class="mt-1 text-[10px] text-amber-500 font-bold">Aplicada sobre o valor original</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                        Juros (R$)
                                        <span x-show="taxas.juros_mensal > 0" class="text-orange-500 normal-case ml-1">
                                            — <span x-text="pct(taxas.juros_mensal)"></span>/mês<span x-show="mesesAtraso > 0"> × <span x-text="mesesAtraso"></span>m</span>
                                        </span>
                                    </label>
                                    <input type="number" name="juros" x-model="juros" min="0" step="0.01" placeholder="0,00"
                                        class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                    <p x-show="mesesAtraso == 0 && vencimento" class="mt-1 text-[10px] text-slate-400">Sem atraso — juros zerado</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                        Honorários (R$)
                                        <span x-show="taxas.honorarios_percentual > 0" x-text="'— ' + pct(taxas.honorarios_percentual)" class="text-blue-500 normal-case ml-1"></span>
                                    </label>
                                    <input type="number" name="honorarios" x-model="honorarios" min="0" step="0.01" placeholder="0,00"
                                        class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Desconto (R$)</label>
                                    <input type="number" name="desconto" x-model="desconto" min="0" step="0.01" placeholder="0,00"
                                        class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status *</label>
                                <select name="status" required class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('status') border-red-400 @enderror">
                                    <option value="aberto" {{ old('status', 'aberto') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                    <option value="pago" {{ old('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                                    <option value="cancelado" {{ old('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Botões --}}
                            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100">
                                <a href="{{ route('tenant.titulos') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                                    Cancelar
                                </a>
                                <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-500/20 transition">
                                    Gerar Título
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Painel Resumo (1/3) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-2xl rounded-3xl border border-slate-100 overflow-hidden" style="position: sticky; top: 2rem;">
                        <div class="px-6 py-5 border-b border-slate-100" style="background: linear-gradient(135deg, #ecfdf5, #f0fdfa);">
                            <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Resumo do Débito</p>
                        </div>
                        <div class="p-6">

                            {{-- Placeholder --}}
                            <div x-show="!devedorId && !valorOriginal" class="text-center py-8">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-400">Selecione um devedor e preencha o valor para ver o cálculo</p>
                            </div>

                            {{-- Taxas do cliente --}}
                            <div x-show="devedorId && taxas.cliente_nome" class="bg-blue-50 rounded-2xl p-4 mb-5">
                                <p class="text-[9px] font-black uppercase tracking-widest text-blue-500 mb-3">Taxas da carteira</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-slate-500">Multa</span>
                                        <span class="font-bold text-slate-700" x-text="pct(taxas.multa_percentual)"></span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-slate-500">Juros/mês</span>
                                        <span class="font-bold text-slate-700" x-text="pct(taxas.juros_mensal)"></span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-slate-500">Honorários</span>
                                        <span class="font-bold text-slate-700" x-text="pct(taxas.honorarios_percentual)"></span>
                                    </div>
                                    <div x-show="vencimento" class="border-t border-blue-200 pt-2 mt-1 flex justify-between text-xs">
                                        <span class="text-slate-500">Atraso</span>
                                        <span class="font-bold" :class="mesesAtraso > 0 ? 'text-red-500' : 'text-emerald-600'"
                                            x-text="mesesAtraso > 0 ? mesesAtraso + ' mês(es)' : 'Em dia'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Cálculo detalhado --}}
                            <div x-show="parseFloat(valorOriginal) > 0" class="space-y-2">
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-xs text-slate-500">Valor Original</span>
                                    <span class="text-sm font-bold text-slate-700" x-text="fmt(valorOriginal)"></span>
                                </div>
                                <div x-show="parseFloat(multa) > 0" class="flex justify-between items-center py-1">
                                    <span class="text-xs text-slate-500">+ Multa (<span x-text="pct(taxas.multa_percentual)"></span>)</span>
                                    <span class="text-sm font-bold text-amber-600" x-text="fmt(multa)"></span>
                                </div>
                                <div x-show="parseFloat(juros) > 0" class="flex justify-between items-center py-1">
                                    <span class="text-xs text-slate-500">+ Juros (<span x-text="mesesAtraso"></span>m × <span x-text="pct(taxas.juros_mensal)"></span>)</span>
                                    <span class="text-sm font-bold text-orange-600" x-text="fmt(juros)"></span>
                                </div>
                                <div x-show="parseFloat(honorarios) > 0" class="flex justify-between items-center py-1">
                                    <span class="text-xs text-slate-500">+ Honorários (<span x-text="pct(taxas.honorarios_percentual)"></span>)</span>
                                    <span class="text-sm font-bold text-blue-600" x-text="fmt(honorarios)"></span>
                                </div>
                                <div x-show="parseFloat(desconto) > 0" class="flex justify-between items-center py-1">
                                    <span class="text-xs text-slate-500">− Desconto</span>
                                    <span class="text-sm font-bold text-green-600" x-text="'− ' + fmt(desconto)"></span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t-2 border-emerald-200 mt-2">
                                    <span class="text-xs font-black uppercase tracking-widest text-slate-600">Total</span>
                                    <span class="text-xl font-black text-emerald-700" x-text="fmt(total)"></span>
                                </div>

                                <p class="text-[10px] text-slate-400 pt-2 leading-relaxed">
                                    Os valores são calculados automaticamente pelas taxas da carteira do devedor.
                                    Você pode ajustá-los manualmente antes de salvar.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function calculadoraTitulo() {
            return {
                devedorId: '{{ old("devedor_id", "") }}',
                valorOriginal: parseFloat('{{ old("valor_original", 0) }}') || 0,
                vencimento: '{{ old("vencimento", "") }}',
                juros: parseFloat('{{ old("juros", 0) }}') || 0,
                multa: parseFloat('{{ old("multa", 0) }}') || 0,
                honorarios: parseFloat('{{ old("honorarios", 0) }}') || 0,
                desconto: parseFloat('{{ old("desconto", 0) }}') || 0,
                taxas: {
                    multa_percentual: 0,
                    juros_mensal: 0,
                    honorarios_percentual: 0,
                    ipca_mensal: 0,
                    cliente_nome: ''
                },
                taxasPorDevedor: @json($taxasPorDevedor),
                mesesAtraso: 0,
                vencido: false,

                get total() {
                    return (parseFloat(this.valorOriginal) || 0) +
                        (parseFloat(this.juros) || 0) +
                        (parseFloat(this.multa) || 0) +
                        (parseFloat(this.honorarios) || 0) -
                        (parseFloat(this.desconto) || 0);
                },

                onDevedorChange() {
                    const t = this.taxasPorDevedor[this.devedorId];
                    this.taxas = t || {
                        multa_percentual: 0,
                        juros_mensal: 0,
                        honorarios_percentual: 0,
                        ipca_mensal: 0,
                        cliente_nome: ''
                    };
                    this.calcular();
                },

                onVencimentoChange() {
                    this.calcularMeses();
                    this.calcular();
                },

                calcularMeses() {
                    if (!this.vencimento) {
                        this.mesesAtraso = 0;
                        this.vencido = false;
                        return;
                    }
                    const hoje = new Date();
                    hoje.setHours(0, 0, 0, 0);
                    const venc = new Date(this.vencimento + 'T00:00:00');
                    if (venc >= hoje) {
                        this.mesesAtraso = 0;
                        this.vencido = false;
                        return;
                    }
                    this.vencido = true;
                    let m = (hoje.getFullYear() - venc.getFullYear()) * 12 + (hoje.getMonth() - venc.getMonth());
                    if (hoje.getDate() < venc.getDate()) m--;
                    this.mesesAtraso = Math.max(0, m);
                },

                calcular() {
                    const v = parseFloat(this.valorOriginal) || 0;
                    if (!v) return;
                    this.multa = this.vencido ? parseFloat((v * (this.taxas.multa_percentual || 0) / 100).toFixed(2)) : 0;
                    this.juros = parseFloat((v * (this.taxas.juros_mensal || 0) / 100 * this.mesesAtraso).toFixed(2));
                    this.honorarios = parseFloat((v * (this.taxas.honorarios_percentual || 0) / 100).toFixed(2));
                },

                fmt(val) {
                    return new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(parseFloat(val) || 0);
                },

                pct(val) {
                    return parseFloat(val || 0).toFixed(2).replace('.', ',') + '%';
                }
            };
        }
    </script>
</x-app-layout>