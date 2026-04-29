<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Setting;
use App\Models\Titulo;
use App\Models\Devedor;
use Illuminate\Http\Request;

class TituloController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'aberto');
        $allowedStatuses = ['aberto', 'negociado', 'pago', 'cancelado'];

        if (!in_array($status, $allowedStatuses)) {
            $status = 'aberto';
        }

        $clientes  = Cliente::orderBy('nome')->get();
        $clienteId = $request->get('cliente_id');
        $busca     = trim($request->get('busca', ''));

        $titulos = Titulo::with(['devedor.cliente', 'acordo'])
            ->where('status', $status)
            ->when($clienteId, fn($q) => $q->whereHas('devedor', fn($dq) => $dq->where('cliente_id', $clienteId)))
            ->when($busca, function ($q) use ($busca) {
                $q->whereHas('devedor', fn($dq) => $dq->where('nome', 'like', "%{$busca}%"))
                    ->orWhere('numero', 'like', "%{$busca}%");
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Contadores para as tabs (respeitam filtro de cliente)
        $counts = Titulo::selectRaw('status, count(*) as total')
            ->when($clienteId, fn($q) => $q->whereHas('devedor', fn($dq) => $dq->where('cliente_id', $clienteId)))
            ->groupBy('status')
            ->pluck('total', 'status');

        $settings = Setting::all()->pluck('value', 'key');

        return view('tenant.titulos.index', compact('titulos', 'status', 'counts', 'settings', 'clientes', 'clienteId', 'busca'));
    }

    public function create()
    {
        $devedores = Devedor::all();
        return view('tenant.titulos.create', compact('devedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'numero' => 'required|string|max:50',
            'valor_original' => 'required|numeric|min:0.01',
            'vencimento' => 'required|date',
            'status' => 'required|string|in:aberto,pago,cancelado,negociado',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'honorarios' => 'nullable|numeric|min:0',
        ]);

        Titulo::create($validated);

        return redirect()->route('tenant.titulos')->with('success', 'Título gerado com sucesso!');
    }

    public function edit(Titulo $titulo)
    {
        $devedores = Devedor::all();
        return view('tenant.titulos.edit', compact('titulo', 'devedores'));
    }

    public function update(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'numero' => 'required|string|max:50',
            'valor_original' => 'required|numeric|min:0.01',
            'vencimento' => 'required|date',
            'status' => 'required|string|in:aberto,pago,cancelado,negociado',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'honorarios' => 'nullable|numeric|min:0',
        ]);

        $titulo->update($validated);

        return redirect()->route('tenant.titulos')->with('success', 'Título atualizado com sucesso!');
    }

    /**
     * Cancela um título com motivo — requer confirmação no front-end antes de chamar.
     */
    public function cancel(Titulo $titulo)
    {
        // Não permite cancelar título já negociado (precisa cancelar o acordo primeiro)
        if ($titulo->status === 'negociado') {
            return back()->with('error', 'Este título está vinculado a um acordo ativo. Cancele o acordo antes de cancelar o título.');
        }

        if ($titulo->status === 'cancelado') {
            return back()->with('error', 'Este título já está cancelado.');
        }

        $titulo->update(['status' => 'cancelado']);

        return back()->with('success', "Título #{$titulo->numero} cancelado com sucesso.");
    }
}
