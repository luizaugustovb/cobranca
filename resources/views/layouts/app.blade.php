<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 selection:bg-indigo-100 selection:text-indigo-900">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
            <!-- Sidebar (Dark Theme remains for Sidebar ONLY for contrast) -->
            @include('layouts.sidebar')

            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen"
                 x-cloak
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden">
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden">
                <!-- Impersonation Banner -->
                @if(session()->has('impersonating_tenant_id'))
                    <div class="bg-indigo-600 px-4 py-2 text-white text-center text-xs font-black uppercase tracking-widest flex flex-wrap justify-between items-center gap-2 shadow-lg sticky top-0 z-50">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            VISUALIZANDO COMO: {{ \App\Models\Tenant::find(session('impersonating_tenant_id'))->name }}
                        </span>
                        <form action="{{ route('admin.stop-impersonation') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-white text-indigo-600 px-3 py-1 rounded-lg font-black hover:bg-indigo-50 transition">
                                SAIR E VOLTAR AO ADMIN
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Top Navbar -->
                <header class="bg-white border-b border-slate-100 sticky top-0 z-30">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-indigo-600 transition p-2 lg:hidden">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                            </button>
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-300 ml-4 hidden md:block italic">Sistema Operacional de Recuperação de Ativos</span>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center group transition">
                                        <div class="mr-3 text-right hidden sm:block leading-none">
                                            <p class="text-[10px] font-black text-slate-800 uppercase tracking-tighter">{{ Auth::user()->name }}</p>
                                            <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest mt-1">
                                                @if(Auth::user()->is_admin && !Auth::user()->tenant_id)
                                                    Admin Geral
                                                @elseif(Auth::user()->tenant_id)
                                                    {{ Auth::user()->tenant?->name ?? 'Admin Tenant' }}
                                                @else
                                                    Operador
                                                @endif
                                            </p>
                                        </div>
                                        <div class="h-9 w-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-black text-[10px] shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')" class="text-[10px] font-black uppercase tracking-widest">Meu Perfil</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-[10px] font-black uppercase tracking-widest text-red-500">Sair</x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <!-- Header de Seção (Optional) -->
                @isset($header)
                    <div class="bg-gray-50 border-b border-slate-100 px-4 sm:px-6 py-4 sm:py-8">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Main Section -->
                <main class="p-4 sm:p-6 lg:p-10">
                    {{ $slot }}
                </main>

                <footer class="mt-auto py-6 px-4 bg-gray-100 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }} - Gestão Inteligente de Recuperação de Ativos.
                    <span class="mx-2 text-gray-300">|</span>
                    Desenvolvido por <span class="font-bold text-indigo-500">LAVB Tecnologias</span>
                </footer>
            </div>
        </div>
    </body>
</html>
