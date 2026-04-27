<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 dark:text-white flex items-center tracking-tighter uppercase leading-none">
                <div class="p-2 bg-blue-100 rounded-lg mr-3 shrink-0">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                Logs de Auditoria
            </h2>
            <a href="{{ route('tenant.relatorios') }}" class="inline-flex items-center text-xs font-black text-slate-400 hover:text-slate-700 uppercase tracking-widest transition shrink-0">
                ← Voltar aos Relatórios
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filtros --}}
            <form method="GET" action="{{ route('tenant.relatorios.auditoria') }}" class="bg-white rounded-3xl shadow-xl border border-slate-100 p-5 sm:p-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Ação</label>
                        <select name="acao" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todas</option>
                            @foreach($acoes as $acao)
                                <option value="{{ $acao }}" {{ request('acao') == $acao ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $acao)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Usuário</label>
                        <input type="text" name="usuario" value="{{ request('usuario') }}" placeholder="Nome do usuário..."
                            class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">De</label>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                            class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Até</label>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                            class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-700 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-blue-500/20">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['acao','usuario','data_inicio','data_fim']))
                    <a href="{{ route('tenant.relatorios.auditoria') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black uppercase tracking-widest transition text-center">
                        Limpar Filtros
                    </a>
                    @endif
                </div>
            </form>

            {{-- Tabela de Logs --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-6 sm:px-10 py-5 sm:py-8 border-b border-slate-50 bg-slate-50/30 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter text-sm sm:text-base">Registro de Atividades</h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $logs->total() }} registro(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-50">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Usuário</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Ação</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400 hidden md:table-cell">Registro</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Data / Hora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($logs as $log)
                            @php
                                $acaoCores = [
                                    'create'  => 'bg-emerald-100 text-emerald-700',
                                    'update'  => 'bg-blue-100 text-blue-700',
                                    'delete'  => 'bg-rose-100 text-rose-700',
                                    'view'    => 'bg-slate-100 text-slate-600',
                                    'login'   => 'bg-blue-100 text-blue-700',
                                    'logout'  => 'bg-slate-100 text-slate-600',
                                ];
                                $corAcao = $acaoCores[$log->action] ?? 'bg-slate-100 text-slate-600';
                                $modelo = $log->auditable_type ? class_basename($log->auditable_type) : '—';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 text-sm font-bold text-slate-800">
                                    {{ $log->user?->name ?? 'Sistema' }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $log->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $corAcao }}">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 hidden md:table-cell">
                                    <span class="font-bold text-slate-700">{{ $modelo }}</span>
                                    @if($log->auditable_id) <span class="text-slate-400">#{{ $log->auditable_id }}</span> @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 text-right">
                                    {{ $log->created_at->translatedFormat('d/m/Y') }}
                                    <div class="font-bold text-slate-500">{{ $log->created_at->format('H:i') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <svg class="w-12 h-12 text-slate-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm text-slate-400">Nenhum log encontrado.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                <div class="px-6 py-5 border-t border-slate-50">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
