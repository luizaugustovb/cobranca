<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase">
                <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                CENTRAL DE COBRANÇA
            </h2>
            <div class="flex space-x-3">
                 <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
                    <a href="{{ route('tenant.titulos', ['status' => 'aberto']) }}" class="px-4 py-2 rounded-lg text-xs font-black uppercase transition {{ $status == 'aberto' ? 'bg-white shadow text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">Abertos</a>
                    <a href="{{ route('tenant.titulos', ['status' => 'pago']) }}" class="px-4 py-2 rounded-lg text-xs font-black uppercase transition {{ $status == 'pago' ? 'bg-white shadow text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">Pagos</a>
                    <a href="{{ route('tenant.titulos', ['status' => 'cancelado']) }}" class="px-4 py-2 rounded-lg text-xs font-black uppercase transition {{ $status == 'cancelado' ? 'bg-white shadow text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">Cancelados</a>
                </div>
                <a href="{{ route('tenant.titulos.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-500/20">
                    Gerar Título
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Nº Título</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Devedor</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Vencimento</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Valor Original</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Encargos</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($titulos as $titulo)
                                <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-tighter">#{{ $titulo->numero }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-tighter">{{ $titulo->devedor->nome }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold tracking-widest">{{ $titulo->devedor->cpf_cnpj }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm {{ $titulo->vencimento->isPast() && $titulo->status == 'aberto' ? 'text-red-500 font-black animate-pulse' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ $titulo->vencimento->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-md font-black text-slate-900 dark:text-white">R$ {{ number_format($titulo->valor_original, 2, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-red-400 font-medium">J+M: R$ {{ number_format(($titulo->juros + $titulo->multa), 2, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('tenant.devedores.show', $titulo->devedor_id) }}" title="Ir para Devedor" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-500 hover:text-white transition shadow-sm">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                                            </a>
                                            <a href="{{ route('tenant.titulos.edit', $titulo) }}" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-500 hover:text-white transition">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 font-medium">
                                        Nenhum título localizado com o status selecionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                    {{ $titulos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
