<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 flex items-center tracking-tighter uppercase">
                <div class="p-2 bg-blue-100 rounded-lg mr-3 shrink-0">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                Acordos Negociados
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl shadow-sm">
                    <p class="font-extrabold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Devedor</th>
                                <th class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Valor Original</th>
                                <th class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Desconto</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Valor Acordo</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Parcelas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($acordos as $acordo)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800 uppercase tracking-tighter">{{ $acordo->devedor->nome ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">{{ $acordo->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        R$ {{ number_format($acordo->valor_original, 2, ',', '.') }}
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-red-500 font-bold">
                                        - R$ {{ number_format($acordo->desconto, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-slate-900">
                                        R$ {{ number_format($acordo->valor_acordo, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-700">
                                            {{ $acordo->parcelas }}x
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColor = match($acordo->status) {
                                                'ativo'     => 'bg-emerald-50 text-emerald-700',
                                                'quitado'   => 'bg-blue-50 text-blue-700',
                                                'cancelado' => 'bg-red-50 text-red-700',
                                                default     => 'bg-slate-100 text-slate-600',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-bold rounded-full uppercase {{ $statusColor }}">
                                            {{ $acordo->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('tenant.acordos.show', $acordo) }}" class="p-2 inline-flex bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition duration-200">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center text-slate-400">
                                            <svg class="w-12 h-12 mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <p class="text-sm font-bold uppercase tracking-widest">Nenhum acordo registrado.</p>
                                            <p class="text-xs mt-1">Para criar um acordo, acesse o perfil de um devedor.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($acordos->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $acordos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
