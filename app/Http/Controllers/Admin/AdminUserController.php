<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::withoutGlobalScopes()->where('is_admin', true)->orWhereNull('tenant_id')->paginate(15);
        return view('admin.users.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_admin'] = true;

        User::withoutGlobalScopes()->create($validated);

        return redirect()->route('admin.users')->with('success', 'Administrador Master criado com sucesso.');
    }

    public function edit($id)
    {
        $admin = User::withoutGlobalScopes()->findOrFail($id);
        return view('admin.users.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.users')->with('success', 'Administrador atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $admin = User::withoutGlobalScopes()->findOrFail($id);
        
        if (auth()->id() === $admin->id) {
            return redirect()->back()->with('error', 'Você não pode excluir a si mesmo.');
        }

        $admin->delete();

        return redirect()->route('admin.users')->with('success', 'Administrador excluído.');
    }
}
