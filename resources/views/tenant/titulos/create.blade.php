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
                    <div class="bg-white shadow-2xl rounded-3xl border border-slate-100">
                        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 rounded-t-3xl">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Preencha os dados do título de cobrança</p>
                        </div>

                        <form method="POST" action="{{ route('tenant.titulos.store') }}" class="p-8 space-y-6">
                            @csrf

                            {{-- Devedor --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Devedor *</label>

                                {{-- Campo oculto que vai no form --}}
                                <input type="hidden" name="devedor_id" x-model="devedorId" required>

                                <div class="relative" @click.away="fechado = true">

                                    {{-- Input de busca --}}
                                    <div @click="abrir()"
                                        class="w-full border rounded-2xl py-3 px-4 flex items-center justify-between cursor-pointer bg-slate-50 transition"
                                        :class="fechado ? 'border-slate-200' : 'border-emerald-500 ring-2 ring-emerald-200'">
                                        <span class="text-sm font-medium text-slate-700 truncate"
                                            x-text="devedorNome || '— Selecione o devedor —'"
                                            :class="devedorNome ? 'text-slate-800' : 'text-slate-400'"></span>
                                        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform" :class="!fechado ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>

                                    {{-- Dropdown --}}
                                    <div x-show="!fechado" x-transition
                                        class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden">

                                        {{-- Campo busca --}}
                                        <div class="p-3 border-b border-slate-100">
                                            <div class="relative">
                                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                <input type="text" x-model="busca" x-ref="inputBusca" @keydown.escape="fechado = true"
                                                    placeholder="Buscar por nome ou CPF/CNPJ..."
                                                    style="padding-left: 2.2rem;"
                                                    class="w-full pr-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                            </div>
                                        </div>

                                        {{-- Lista --}}
                                        <ul class="max-h-56 overflow-y-auto py-1">
                                            <template x-for="d in filtrados()" :key="d.id">
                                                <li @click="selecionar(d)"
                                                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-emerald-50 flex items-center justify-between"
                                                    :class="devedorId == d.id ? 'bg-emerald-50 font-bold text-emerald-700' : 'text-slate-700'">
                                                    <span x-text="d.nome"></span>
                                                    <span class="text-xs text-slate-400 font-mono ml-2" x-text="d.cpf"></span>
                                                </li>
                                            </template>
                                            <li x-show="filtrados().length === 0" class="px-4 py-4 text-sm text-slate-400 text-center">
                                                Nenhum devedor encontrado
                                            </li>
                                        </ul>
                                    </div>
                                </div>

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

    @php
    $devedoresJson = json_encode($devedores->map(fn($d) => ['id' => $d->id, 'nome' => $d->nome, 'cpf' => $d->cpf_cnpj])->values());
    $taxasJson = json_encode($taxasPorDevedor);
    $oldDevedorId = json_encode(old('devedor_id', ''));
    $oldValor = (float) old('valor_original', 0);
    $oldVenc = json_encode(old('vencimento', ''));
    $oldJuros = (float) old('juros', 0);
    $oldMulta = (float) old('multa', 0);
    $oldHonorarios = (float) old('honorarios', 0);
    $oldDesconto = (float) old('desconto', 0);
    @endphp

    <script>
        document.addEventListener('alpine:init', () => {
            const _lista = {
                !!$devedoresJson!!
            };
            const _taxas = {
                !!$taxasJson!!
            };

            Alpine.data('calculadoraTitulo', () => ({
                devedorId: {
                    !!$oldDevedorId!!
                },
                busca: '',
                fechado: true,
                valorOriginal: {
                    {
                        $oldValor
                    }
                },
                vencimento: {
                    !!$oldVenc!!
                },
                juros: {
                    {
                        $oldJuros
                    }
                },
                multa: {
                    {
                        $oldMulta
                    }
                },
                honorarios: {
                    {
                        $oldHonorarios
                    }
                },
                desconto: {
                    {
                        $oldDesconto
                    }
                },
                taxas: {
                    multa_percentual: 0,
                    juros_mensal: 0,
                    honorarios_percentual: 0,
                    ipca_mensal: 0,
                    cliente_nome: ''
                },
                taxasPorDevedor: _taxas,
                mesesAtraso: 0,
                vencido: false,

                get devedorNome() {
                    const d = _lista.find(x => String(x.id) === String(this.devedorId));
                    return d ? d.nome + ' \u2014 ' + d.cpf : '';
                },

                filtrados() {
                    if (!this.busca) return _lista;
                    const t = this.busca.toLowerCase();
                    return _lista.filter(d =>
                        d.nome.toLowerCase().includes(t) || d.cpf.toLowerCase().includes(t)
                    );
                },

                selecionar(d) {
                    this.devedorId = String(d.id);
                    this.fechado = true;
                    this.busca = '';
                    this.onDevedorChange();
                },

                abrir() {
                    this.fechado = false;
                    this.$nextTick(() => this.$refs.inputBusca && this.$refs.inputBusca.focus());
                },

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
            }));
        });
    </script>
</x-app-layout>