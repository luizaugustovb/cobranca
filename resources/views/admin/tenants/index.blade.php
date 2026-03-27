<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-900 flex items-center tracking-tighter uppercase leading-none">
                <div class="p-2 bg-indigo-600 rounded-lg mr-3 shadow-lg shadow-indigo-500/20 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                GESTÃO DE ESCRITÓRIOS
            </h2>
            <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center px-6 py-3 bg-slate-900 border border-transparent rounded-xl font-black text-white uppercase text-[10px] tracking-widest hover:bg-black transition shadow-xl shadow-black/10">
                Novo Escritório
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Escritório / Slug</th>
                                <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">CNPJ / Plano</th>
                                <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                                <th scope="col" class="px-8 py-5 text-right text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse ($tenants as $tenant)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-slate-800 uppercase tracking-tighter">{{ $tenant->name }}</div>
                                        <div class="text-[10px] text-indigo-500 font-bold tracking-widest">{{ $tenant->slug }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-xs font-bold text-slate-500">{{ $tenant->document }}</div>
                                        <div class="text-[10px] text-slate-400 font-black uppercase">{{ $tenant->plan }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 inline-flex text-[9px] font-black rounded-lg {{ $tenant->status == 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} uppercase tracking-widest">
                                            {{ $tenant->status == 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right space-x-3 flex items-center justify-end">
                                         <a href="{{ route('admin.impersonate', $tenant) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition shadow-lg shadow-indigo-500/20">
                                             Impersonar
                                         </a>
                                         <a href="{{ route('admin.tenants.edit', $tenant) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-100 inline-flex align-middle">
                                             <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                         </a>
                                         
                                         @if($tenant->status === 'inactive')
                                             <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este escritório?')">
                                                 @csrf @method('DELETE')
                                                 <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition">
                                                     <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                 </button>
                                             </form>
                                         @endif
                                     </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                       <span class="text-xs font-black text-slate-300 uppercase underline decoration-indigo-200">Nenhum tenant registrado no core</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-gray-100">
                    {{ $tenants->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
