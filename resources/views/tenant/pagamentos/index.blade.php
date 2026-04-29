<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
                <div class="p-2 bg-green-100 rounded-lg mr-3 shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                EXTRATO DE PAGAMENTOS
            </h2>
            <a href="{{ route('tenant.pagamentos.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-slate-900 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-black transition shadow-lg text-sm">
                Lançar Recebimento
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filtro por cliente --}}
            <form method="GET" action="{{ route('tenant.pagamentos') }}" class="mb-6 flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="busca" value="{{ $busca }}"
                        placeholder="Buscar por nome do devedor..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:text-white" />
                </div>
                <select name="cliente_id"
                    class="sm:w-64 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    onchange="this.form.submit()">
                    <option value="">Todos os clientes</option>
                    @foreach($clientes as $c)
                    <option value="{{ $c->id }}" {{ $clienteId == $c->id ? 'selected' : '' }}>{{ $c->nome }}</option>
                    @endforeach
                </select>
                @if($clienteId || $busca)
                <a href="{{ route('tenant.pagamentos') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-sm font-bold hover:bg-gray-200 transition whitespace-nowrap">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Limpar
                </a>
                @endif
            </form>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">ID / Data</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Devedor</th>
                                <th scope="col" class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Carteira / Cliente</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Valor Recebido</th>
                                <th scope="col" class="hidden sm:table-cell px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Forma</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($pagamentos as $pagamento)
                            <tr class="hover:bg-green-50/20 dark:hover:bg-green-900/10 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter">#{{ $pagamento->id }}</div>
                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $pagamento->data_pagamento->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-tighter">{{ $pagamento->acordo->devedor->nome }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold tracking-widest">Acordo #{{ $pagamento->acordo_id }}</div>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 mr-2 shrink-0"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $pagamento->acordo->devedor->cliente->nome ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-black text-green-600">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</div>
                                </td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        @if($pagamento->forma_pagamento == 'pix')
                                        <span class="p-1 px-2 bg-blue-100 text-blue-700 rounded-md text-[10px] font-black uppercase tracking-tight">PIX</span>
                                        @elseif($pagamento->forma_pagamento == 'boleto')
                                        <span class="p-1 px-2 bg-gray-100 text-gray-700 rounded-md text-[10px] font-black uppercase tracking-tight">Boleto</span>
                                        @else
                                        <span class="p-1 px-2 bg-blue-100 text-blue-700 rounded-md text-[10px] font-black uppercase tracking-tight">{{ $pagamento->forma_pagamento }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-green-500/10 text-green-500 border border-green-500/20">
                                        Confirmado
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-400 italic font-medium">
                                    Nenhum pagamento registrado nos filtros atuais.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                    {{ $pagamentos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>