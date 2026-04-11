<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-black text-xl sm:text-3xl text-slate-800 flex items-center tracking-tighter uppercase">
                <div class="p-2 bg-purple-100 rounded-lg mr-3 shrink-0">
                    <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                UsuÃ¡rios do EscritÃ³rio
            </h2>
            <a href="{{ route('tenant.usuarios.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-slate-900 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-black transition shadow-lg text-sm">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Novo UsuÃ¡rio
            </a>
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
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">UsuÃ¡rio</th>
                                <th class="hidden sm:table-cell px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Contato</th>
                                <th class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Ãšltimo Acesso</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">AÃ§Ãµes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($usuarios as $usuario)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-black text-xs mr-3">
                                                {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800 uppercase tracking-tighter">{{ $usuario->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $usuario->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ $usuario->phone ?? '-' }}
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                        {{ $usuario->last_login_at ? \Carbon\Carbon::parse($usuario->last_login_at)->format('d/m/Y H:i') : 'Nunca' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full uppercase {{ $usuario->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                            {{ $usuario->status === 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                        @if($usuario->id === auth()->id())
                                            <span class="ml-1 px-2 py-1 text-xs font-bold rounded-full bg-indigo-50 text-indigo-600 uppercase">VocÃª</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('tenant.usuarios.edit', $usuario) }}" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition duration-200">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            @if($usuario->id !== auth()->id())
                                                <form action="{{ route('tenant.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('Excluir este usuÃ¡rio?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-500 hover:text-white transition duration-200">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center text-slate-400">
                                            <svg class="w-12 h-12 mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            <p class="text-sm font-bold uppercase tracking-widest">Nenhum usuÃ¡rio cadastrado.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($usuarios->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $usuarios->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
