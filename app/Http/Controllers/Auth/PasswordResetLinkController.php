<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }

    /**
     * Handle an incoming password reset link request via WhatsApp.
     */
    public function storeWhatsApp(Request $request): RedirectResponse
    {
        $request->validate([
            'document' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        // Normalização: Apenas números
        $doc = preg_replace('/[^0-9]/', '', $request->document);
        $phoneInput = preg_replace('/[^0-9]/', '', $request->phone);

        // Busca na tabela users e na tabela tenants (caso o telefone esteja salvo no escritório)
        $user = \App\Models\User::withoutGlobalScopes()->where('document', $doc)
            ->where(function($query) use ($phoneInput) {
                $query->where('phone', $phoneInput)
                      ->orWhere('phone', '55' . $phoneInput)
                      ->orWhere('phone', substr($phoneInput, 2));
            })->first();

        if (!$user) {
            $user = \App\Models\User::withoutGlobalScopes()->whereHas('tenant', function($query) use ($doc, $phoneInput) {
                $query->where('document', $doc)
                      ->where(function($q) use ($phoneInput) {
                          $q->where('phone', $phoneInput)
                            ->orWhere('phone', '55' . $phoneInput)
                            ->orWhere('phone', substr($phoneInput, 2));
                      });
            })->first();
        }

        if (!$user) {
            return back()->withErrors(['document' => 'Seus dados de recuperação (CPF/WhatsApp) não conferem. Verifique e tente novamente.'])
                         ->withInput();
        }

        // Gera nova senha temporária
        $tempPassword = \Illuminate\Support\Str::random(8);
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($tempPassword)]);

        // Dispara WhatsApp (Mock)
        $wa = new \App\Services\WhatsAppService();
        $message = "🔹 *COBRANÇAPRO* 🔹\n\nSua nova senha de acesso é: *{$tempPassword}*\n\nAo entrar, acesse seu perfil para alterá-la. \nLink de acesso: ".url('/login');
        
        $phoneToSend = $user->phone ?? ($user->tenant->phone ?? $phoneInput);
        $wa->sendMessage($phoneToSend, $message);

        return back()->with('status', 'Uma nova senha foi enviada para o seu WhatsApp!');
    }
}
