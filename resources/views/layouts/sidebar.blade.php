<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 bg-[#0f172a] w-64 h-full z-40 lg:static lg:translate-x-0 shadow-2xl flex flex-col border-r border-slate-800 transition-transform duration-300">
    
    <!-- Branding Header -->
    <div class="px-6 py-6 flex items-center justify-center border-b border-slate-800/50">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center group">
            @if(file_exists(public_path('logo2.png')))
                <img src="{{ asset('logo2.png') }}" alt="Logo" class="h-12 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:rotate-6 transition-transform duration-500">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="leading-none">
                        <span class="text-lg font-black text-white tracking-tighter uppercase block">COBRANÇA<span class="text-indigo-400">PRO</span></span>
                        <span class="text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] mt-1 block italic">SaaS Recovery</span>
                    </div>
                </div>
            @endif
        </a>
    </div>

    <!-- Scrollable Menu -->
    <div class="flex-grow overflow-y-auto px-4 py-6 space-y-8 custom-scrollbar">
        
        <!-- Admin Global Perspective -->
        @if(auth()->user()->is_admin && !session()->has('impersonating_tenant_id'))
            <div>
                <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Administração SaaS</p>
                <nav class="space-y-2">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'>
                        Resumo Global
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.tenants')" :active="request()->routeIs('admin.tenants*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'>
                        Gestão Escritórios
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.financeiro')" :active="request()->routeIs('admin.financeiro*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'>
                        Receitas SaaS
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.planos')" :active="request()->routeIs('admin.planos*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'>
                        Planos & Preços
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>'>
                        Configurações
                    </x-sidebar-link>
                </nav>
            </div>
        @endif

        <!-- Tenant Perspective -->
        @if(auth()->user()->tenant_id || session()->has('impersonating_tenant_id'))
            <div>
                <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Operação</p>
                <nav class="space-y-2">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'>
                        Painel Controle
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.clientes')" :active="request()->routeIs('tenant.clientes*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'>
                        Meus Clientes
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.devedores')" :active="request()->routeIs('tenant.devedores*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'>
                        Devedores
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.titulos')" :active="request()->routeIs('tenant.titulos*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'>
                        Cobrança
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.acordos')" :active="request()->routeIs('tenant.acordos*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7M8 7h12m0 0v8a2 2 0 01-2 2h-2M9 21h6"/></svg>'>
                        Acordos
                    </x-sidebar-link>
                </nav>
            </div>

            <div>
                <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Escritório</p>
                <nav class="space-y-2">
                    <x-sidebar-link :href="route('tenant.pagamentos')" :active="request()->routeIs('tenant.pagamentos*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'>
                        Recebimentos
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.importacoes')" :active="request()->routeIs('tenant.importacoes*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>'>
                        Importar Dados
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.relatorios')" :active="request()->routeIs('tenant.relatorios*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'>
                        Relatórios
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.settings')" :active="request()->routeIs('tenant.settings*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'>
                        Configurações
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('tenant.usuarios')" :active="request()->routeIs('tenant.usuarios*')" icon='<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'>
                        Equipe
                    </x-sidebar-link>
                </nav>
            </div>
        @endif

    </div>

    <!-- Exit Impersonation Button explicitly at bottom of sidebar too -->
    @if(session()->has('impersonating_tenant_id'))
        <div class="p-6 border-t border-slate-800 bg-slate-900/50">
            <form action="{{ route('admin.stop-impersonation') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-rose-600 text-white rounded-xl font-black uppercase text-xs tracking-widest hover:bg-rose-700 transition shadow-lg shadow-rose-500/20">
                    Encerrar Suporte
                </button>
            </form>
        </div>
    @endif
</aside>
