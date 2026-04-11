<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <h2 class="font-black text-lg sm:text-2xl text-slate-900 tracking-tighter uppercase leading-none">
                Gestão Mestra de <span class="text-emerald-600">Usuários</span>
            </h2>
            <a href="{{ route('admin.users.create') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-500/20 font-black uppercase text-[10px] tracking-widest text-white transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Cadastrar CEO / Master
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5H7v5m10 0H7"/></svg>
                        </div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest leading-none">Membros com Acesso Root</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Master / Contato</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Documento (CPF)</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Telefone (WhatsApp)</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Governança</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            @forelse($admins as $admin)
                                <tr class="hover:bg-slate-50/30 transition group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                                {{ substr($admin->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-black text-slate-900">{{ $admin->name }}</div>
                                                <div class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">{{ $admin->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full bg-slate-100 text-slate-500">
                                            {{ $admin->document ?? 'Não Informado' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full bg-emerald-50 text-emerald-600">
                                            {{ $admin->phone ?? 'Não Informado' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end space-x-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('admin.users.edit', $admin->id) }}" class="p-2 bg-white text-indigo-500 hover:bg-slate-50 rounded-xl shadow-sm border border-slate-100 transition" title="Editar">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </a>
                                            @if($admin->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" class="inline-block" onsubmit="return confirm('ATENÇÃO CIVIL/CRIMINAL: A exclusão revogará todos os acessos master. Tem certeza?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-white text-rose-500 hover:bg-rose-50 rounded-xl shadow-sm border border-rose-100 transition" title="Deletar">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="mx-auto w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 mb-4">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </div>
                                        <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Nenhum administrador encontrado além da matriz originadora.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($admins->hasPages())
                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $admins->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
