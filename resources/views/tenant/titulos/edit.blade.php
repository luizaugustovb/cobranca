<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 tracking-tighter uppercase flex items-center">
                <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                Editar Título #{{ $titulo->numero }}
            </h2>
            <a href="{{ route('tenant.titulos') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-3xl border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Edite os dados do título de cobrança</p>
                </div>

                <form method="POST" action="{{ route('tenant.titulos.update', $titulo) }}" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Devedor --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Devedor *</label>
                        <select name="devedor_id" required class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('devedor_id') border-red-400 @enderror">
                            <option value="">— Selecione o devedor —</option>
                            @foreach($devedores as $devedor)
                                <option value="{{ $devedor->id }}" {{ old('devedor_id', $titulo->devedor_id) == $devedor->id ? 'selected' : '' }}>
                                    {{ $devedor->nome }} — {{ $devedor->cpf_cnpj }}
                                </option>
                            @endforeach
                        </select>
                        @error('devedor_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Número do Título --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Número do Título *</label>
                        <input type="text" name="numero" value="{{ old('numero', $titulo->numero) }}" required
                            class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('numero') border-red-400 @enderror">
                        @error('numero')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Valor e Vencimento --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Valor Original (R$) *</label>
                            <input type="number" name="valor_original" value="{{ old('valor_original', $titulo->valor_original) }}" required min="0.01" step="0.01"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('valor_original') border-red-400 @enderror">
                            @error('valor_original')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Vencimento *</label>
                            <input type="date" name="vencimento" value="{{ old('vencimento', $titulo->vencimento->format('Y-m-d')) }}" required
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('vencimento') border-red-400 @enderror">
                            @error('vencimento')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Juros, Multa, Desconto e Honorários --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Juros (R$)</label>
                            <input type="number" name="juros" value="{{ old('juros', $titulo->juros) }}" min="0" step="0.01"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Multa (R$)</label>
                            <input type="number" name="multa" value="{{ old('multa', $titulo->multa) }}" min="0" step="0.01"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Desconto (R$)</label>
                            <input type="number" name="desconto" value="{{ old('desconto', $titulo->desconto) }}" min="0" step="0.01"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Honorários Advocatícios (R$)</label>
                            <input type="number" name="honorarios" value="{{ old('honorarios', $titulo->honorarios) }}" min="0" step="0.01"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status *</label>
                        <select name="status" required class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('status') border-red-400 @enderror">
                            <option value="aberto"    {{ old('status', $titulo->status) == 'aberto'    ? 'selected' : '' }}>Aberto</option>
                            <option value="negociado" {{ old('status', $titulo->status) == 'negociado' ? 'selected' : '' }}>Negociado</option>
                            <option value="pago"      {{ old('status', $titulo->status) == 'pago'      ? 'selected' : '' }}>Pago</option>
                            <option value="cancelado" {{ old('status', $titulo->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Botões --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <a href="{{ route('tenant.titulos') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-500/20 transition">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
