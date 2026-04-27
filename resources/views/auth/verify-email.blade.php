<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Validar <span class="text-blue-600">E-mail</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2 px-6">Segurança e Autenticidade CobrançaPro.</p>
    </div>

    <div class="mb-6 text-sm text-slate-500 text-center leading-relaxed">
        {{ __('Obrigado por se cadastrar! Antes de começar, você poderia verificar seu endereço de e-mail clicando no link que acabamos de enviar para você? Se você não recebeu o e-mail, teremos o prazer de enviar outro.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 font-black text-[10px] uppercase tracking-widest text-emerald-600 bg-emerald-50 p-4 rounded-xl text-center border border-emerald-100">
            {{ __('Um novo link de verificação foi enviado para o endereço de e-mail fornecido durante o cadastro.') }}
        </div>
    @endif

    <div class="mt-8 flex flex-col space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full py-5 bg-blue-600 hover:bg-slate-900 rounded-2xl shadow-xl shadow-blue-500/20 flex justify-center text-sm font-black uppercase tracking-[0.2em] transition">
                {{ __('Reenviar E-mail de Verificação') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition underline decoration-dotted">
                {{ __('Sair do Sistema') }}
            </button>
        </form>
    </div>
</x-guest-layout>
