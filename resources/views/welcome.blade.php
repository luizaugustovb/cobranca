<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CobrançaPro - Gestão Inteligente de Recuperação Financeira</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Outfit', sans-serif; }
            .bg-glass { backdrop-filter: blur(16px) saturate(180%); -webkit-backdrop-filter: blur(16px) saturate(180%); background-color: rgba(255, 255, 255, 0.75); }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 selection:bg-blue-600 selection:text-white">
        
        <!-- Navbar -->
        <nav class="fixed top-0 w-full z-50 bg-glass border-b border-slate-200/50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase leading-none">COBRANÇA<span class="text-blue-600">PRO</span></span>
                </div>
                <div class="hidden md:flex items-center space-x-8 text-xs font-black uppercase tracking-widest text-slate-500">
                    <a href="#features" class="hover:text-blue-600 transition">Recursos</a>
                    <a href="#solucoes" class="hover:text-blue-600 transition">Soluções</a>
                    <a href="#precos" class="hover:text-blue-600 transition">Planos</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all shadow-xl shadow-black/10">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-black uppercase tracking-widest text-slate-900 hover:text-blue-600 transition">Login</a>
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-700 transition shadow-xl shadow-blue-500/20">Solicitar Demonstração</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="pt-48 pb-32 px-6 relative overflow-hidden bg-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(59,130,246,0.1),transparent)] pointer-events-none"></div>
            <div class="max-w-5xl mx-auto text-center relative z-10">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-6 border border-blue-100 shadow-sm animate-bounce">A Nova Era da Recuperação Financeira</span>
                <h1 class="text-6xl md:text-8xl font-black text-slate-900 tracking-tighter mb-8 leading-[0.9] uppercase italic transform -skew-x-2">Recupere capital com <span class="text-blue-600 italic">inteligência artificial.</span></h1>
                <p class="text-xl text-slate-400 font-medium max-w-2xl mx-auto mb-12">Plataforma multi-tenant de alta performance para escritórios de cobrança, assessorias jurídicas e empresas de fomento mercantil.</p>
                <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full md:w-auto px-10 py-5 bg-blue-600 text-white rounded-3xl font-black uppercase text-sm tracking-widest hover:bg-blue-700 transition-all hover:scale-105 shadow-2xl shadow-blue-500/30">Começar Agora Grátis</a>
                    <a href="#" class="w-full md:w-auto px-10 py-5 bg-slate-100 text-slate-800 rounded-3xl font-black uppercase text-sm tracking-widest hover:bg-slate-200 transition-all border border-slate-200">Ver Vídeo Demo</a>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="features" class="py-32 px-6 bg-slate-50">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <div class="p-10 bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl transition duration-500 transform hover:-translate-y-4">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <h3 class="text-2xl font-black tracking-tighter uppercase mb-4">Negociação Digital</h3>
                        <p class="text-slate-400 font-medium">Calculadora avançada de juros, multas e parcelamento automático com geração de acordos em segundos.</p>
                    </div>
                    <div class="p-10 bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl transition duration-500 transform hover:-translate-y-4">
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
                        <h3 class="text-2xl font-black tracking-tighter uppercase mb-4">Omnichannel</h3>
                        <p class="text-slate-400 font-medium">Notificações automáticas via WhatsApp e E-mail para aumentar em até 40% a taxa de recebimento dos devedores.</p>
                    </div>
                    <div class="p-10 bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl transition duration-500 transform hover:-translate-y-4">
                        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                        <h3 class="text-2xl font-black tracking-tighter uppercase mb-4">API & Webhooks</h3>
                        <p class="text-slate-400 font-medium">Integração nativa com Asaas e Viicio para baixas automáticas em tempo real direto na conta corrente.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-slate-900 text-white text-center">
            <p class="text-[10px] font-black uppercase tracking-[0.4em] opacity-40">&copy; {{ date('Y') }} COBRANÇAPRO • SAAS MULTITENANT PREMIUM</p>
        </footer>

    </body>
</html>
