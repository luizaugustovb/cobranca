<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 tracking-tighter uppercase flex items-center">
                <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                Gerar Novo TÃ­tulo
            </h2>
            <a href="{{ route('tenant.titulos') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                â† Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-3xl border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Preencha os dados do tÃ­tulo de cobranÃ§a</p>
                </div>

                <form method="POST" action="{{ route('tenant.titulos.store') }}" class="p-8 space-y-6">
                    @csrf

                    {{-- Devedor --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Devedor *</label>
                        <select name="devedor_id" required class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('devedor_id') border-red-400 @enderror">
                            <option value="">â€” Selecione o devedor â€”</option>
                            @foreach($devedores as $devedor)
                                <option value="{{ $devedor->id }}" {{ old('devedor_id') == $devedor->id ? 'selected' : '' }}>
                                    {{ $devedor->nome }} â€” {{ $devedor->cpf_cnpj }}
                                </option>
                            @endforeach
                        </select>
                        @error('devedor_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- NÃºmero do TÃ­tulo --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">NÃºmero do TÃ­tulo *</label>
                        <input type="text" name="numero" value="{{ old('numero') }}" required placeholder="Ex: 0001/2026"
                            class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('numero') border-red-400 @enderror">
                        @error('numero')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Valor e Vencimento --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Valor Original (R$) *</label>
                            <input type="number" name="valor_original" value="{{ old('valor_original') }}" required min="0.01" step="0.01" placeholder="0,00"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('valor_original') border-red-400 @enderror">
                            @error('valor_original')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Vencimento *</label>
                            <input type="date" name="vencimento" value="{{ old('vencimento') }}" required
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('vencimento') border-red-400 @enderror">
                            @error('vencimento')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Juros, Multa, Desconto e HonorÃ¡rios --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Juros (R$)</label>
                            <input type="number" name="juros" value="{{ old('juros', 0) }}" min="0" step="0.01" placeholder="0,00"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Multa (R$)</label>
                            <input type="number" name="multa" value="{{ old('multa', 0) }}" min="0" step="0.01" placeholder="0,00"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Desconto (R$)</label>
                            <input type="number" name="desconto" value="{{ old('desconto', 0) }}" min="0" step="0.01" placeholder="0,00"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">HonorÃ¡rios AdvocatÃ­cios (R$)</label>
                            <input type="number" name="honorarios" value="{{ old('honorarios', 0) }}" min="0" step="0.01" placeholder="0,00"
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

                    {{-- BotÃµes --}}
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('tenant.titulos') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-500/20 transition">
                            Gerar TÃ­tulo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
