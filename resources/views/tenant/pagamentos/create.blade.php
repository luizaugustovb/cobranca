<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 tracking-tighter uppercase flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                LanÃ§ar Recebimento
            </h2>
            <a href="{{ route('tenant.pagamentos') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                â† Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-3xl border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Registro manual de pagamento (PIX, dinheiro, transferÃªncia)</p>
                </div>

                <form method="POST" action="{{ route('tenant.pagamentos.store') }}" class="p-8 space-y-6" x-data="{ selectedAcordoId: '', parcelas: [] }">
                    @csrf

                    {{-- Acordo --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Acordo *</label>
                        <select name="acordo_id" required x-model="selectedAcordoId"
                            @change="parcelas = $el.options[$el.selectedIndex].dataset.parcelas ? JSON.parse($el.options[$el.selectedIndex].dataset.parcelas) : []"
                            class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('acordo_id') border-red-400 @enderror">
                            <option value="">â€” Selecione o acordo â€”</option>
                            @foreach($acordos as $acordo)
                                <option value="{{ $acordo->id }}"
                                    data-parcelas="{{ json_encode($acordo->acordoParcelas->map(fn($p) => ['id' => $p->id, 'numero' => $p->numero_parcela, 'valor' => $p->valor, 'status' => $p->status])) }}"
                                    {{ old('acordo_id') == $acordo->id ? 'selected' : '' }}>
                                    {{ $acordo->devedor->nome }} â€” Acordo #{{ $acordo->id }} (R$ {{ number_format($acordo->valor_total, 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('acordo_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Parcela (opcional) --}}
                    <div x-show="parcelas.length > 0">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Parcela (opcional)</label>
                        <select name="parcela_id"
                            class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="">â€” Pagamento avulso (sem vincular parcela) â€”</option>
                            <template x-for="p in parcelas" :key="p.id">
                                <option :value="p.id" :disabled="p.status === 'pago'">
                                    <span x-text="'Parcela ' + p.numero + ' â€” R$ ' + parseFloat(p.valor).toFixed(2).replace('.', ',') + (p.status === 'pago' ? ' (jÃ¡ paga)' : '')"></span>
                                </option>
                            </template>
                        </select>
                    </div>

                    {{-- Valor e Data --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Valor Recebido (R$) *</label>
                            <input type="number" name="valor" value="{{ old('valor') }}" required min="0.01" step="0.01" placeholder="0,00"
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('valor') border-red-400 @enderror">
                            @error('valor')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data do Pagamento *</label>
                            <input type="date" name="data_pagamento" value="{{ old('data_pagamento', now()->format('Y-m-d')) }}" required
                                class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('data_pagamento') border-red-400 @enderror">
                            @error('data_pagamento')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Forma de Pagamento --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Forma de Pagamento *</label>
                        <select name="forma_pagamento" required
                            class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('forma_pagamento') border-red-400 @enderror">
                            <option value="">â€” Selecione â€”</option>
                            <option value="pix" {{ old('forma_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="dinheiro" {{ old('forma_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="transferencia" {{ old('forma_pagamento') == 'transferencia' ? 'selected' : '' }}>TransferÃªncia BancÃ¡ria</option>
                            <option value="boleto" {{ old('forma_pagamento') == 'boleto' ? 'selected' : '' }}>Boleto</option>
                            <option value="cartao_credito" {{ old('forma_pagamento') == 'cartao_credito' ? 'selected' : '' }}>CartÃ£o de CrÃ©dito</option>
                            <option value="cartao_debito" {{ old('forma_pagamento') == 'cartao_debito' ? 'selected' : '' }}>CartÃ£o de DÃ©bito</option>
                            <option value="outros" {{ old('forma_pagamento') == 'outros' ? 'selected' : '' }}>Outros</option>
                        </select>
                        @error('forma_pagamento')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- ID Gateway (opcional) --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">ID do Gateway (opcional)</label>
                        <input type="text" name="gateway_id" value="{{ old('gateway_id') }}" placeholder="Ex: txid do PIX, cÃ³digo do boleto..."
                            class="w-full border border-slate-200 rounded-2xl py-3 px-4 text-sm font-medium text-slate-700 bg-slate-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    {{-- BotÃµes --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <a href="{{ route('tenant.pagamentos') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-green-700 shadow-lg shadow-green-500/20 transition">
                            Registrar Recebimento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
