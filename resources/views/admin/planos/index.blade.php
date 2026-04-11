<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-black text-lg sm:text-2xl text-slate-900 leading-none uppercase tracking-tighter">
                Planos & PreÃ§os
            </h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2 px-1">Gerencie os valores e configuraÃ§Ãµes dos planos SaaS</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-sm font-bold text-emerald-700">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.planos.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex items-center space-x-3 bg-indigo-50/20">
                        <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Valores dos Planos</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">Altere o valor mensal de cada plano. As prÃ³ximas cobranÃ§as usarÃ£o os valores salvos aqui.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-50">
                        @foreach($plans as $i => $plan)
                        <div class="p-8 hover:bg-slate-50/50 transition">
                            <input type="hidden" name="plans[{{ $i }}][id]" value="{{ $plan->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                                <!-- Slug (readonly) -->
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Identificador</label>
                                    <div class="px-4 py-3 bg-slate-100 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-500">
                                        {{ $plan->slug }}
                                    </div>
                                </div>

                                <!-- Nome -->
                                <div class="space-y-2">
                                    <label for="nome_{{ $i }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400">Nome Exibido</label>
                                    <input
                                        id="nome_{{ $i }}"
                                        type="text"
                                        name="plans[{{ $i }}][nome]"
                                        value="{{ old("plans.{$i}.nome", $plan->nome) }}"
                                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    />
                                    @error("plans.{$i}.nome")
                                        <p class="text-[10px] text-red-500 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Valor -->
                                <div class="space-y-2">
                                    <label for="valor_{{ $i }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400">Valor Mensal (R$)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400">R$</span>
                                        <input
                                            id="valor_{{ $i }}"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="plans[{{ $i }}][valor]"
                                            value="{{ old("plans.{$i}.valor", number_format($plan->valor, 2, '.', '')) }}"
                                            class="w-full bg-white border border-indigo-100 rounded-2xl py-3 pl-10 pr-4 text-sm font-black text-slate-900 focus:ring-indigo-500 focus:border-indigo-500"
                                            required
                                        />
                                    </div>
                                    @error("plans.{$i}.valor")
                                        <p class="text-[10px] text-red-500 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Linha secundÃ¡ria: Viicio Plan ID -->
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-start-3 space-y-2">
                                    <label for="viicio_{{ $i }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400">ID do Plano no Viicio</label>
                                    <input
                                        id="viicio_{{ $i }}"
                                        type="number"
                                        min="1"
                                        name="plans[{{ $i }}][viicio_plan_id]"
                                        value="{{ old("plans.{$i}.viicio_plan_id", $plan->viicio_plan_id) }}"
                                        placeholder="Ex: 1"
                                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-2.5 px-4 text-sm font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition shadow-lg shadow-indigo-500/20">
                            Salvar Valores dos Planos
                        </button>
                    </div>
                </div>

            </form>

            <!-- Info sobre uso dos planos -->
            <div class="mt-6 p-6 bg-amber-50 border border-amber-100 rounded-2xl flex items-start space-x-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-amber-700 font-semibold">
                    Os valores alterados aqui serÃ£o usados automaticamente ao cadastrar novos escritÃ³rios.
                    CobranÃ§as jÃ¡ geradas <strong>nÃ£o sÃ£o afetadas retroativamente</strong>.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
