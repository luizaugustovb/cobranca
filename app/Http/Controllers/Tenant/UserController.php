<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $usuarios = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->paginate(15);

        return view('tenant.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('tenant.usuarios.create');
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', Password::min(8)],
            'status'   => 'required|in:active,inactive',
        ]);

        User::create([
            'tenant_id'         => $tenantId,
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'status'            => $request->status,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('tenant.usuarios')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        // Garante que o usuário pertence ao tenant do operador logado
        abort_if($user->tenant_id !== auth()->user()->tenant_id, 403);

        return view('tenant.usuarios.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->tenant_id !== auth()->user()->tenant_id, 403);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'password' => ['nullable', Password::min(8)],
            'status'   => 'required|in:active,inactive',
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('tenant.usuarios')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        abort_if($user->tenant_id !== auth()->user()->tenant_id, 403);
        // Impede excluir a si mesmo
        abort_if($user->id === auth()->id(), 403, 'Você não pode excluir seu próprio usuário.');

        $user->delete();

        return redirect()->route('tenant.usuarios')->with('success', 'Usuário removido.');
    }
}
