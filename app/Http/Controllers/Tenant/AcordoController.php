<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Acordo;
use App\Models\AcordoParcela;
use App\Models\Devedor;
use App\Models\Titulo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AcordoController extends Controller
{
    public function index()
    {
        $acordos = Acordo::with('devedor')->orderBy('created_at', 'desc')->paginate(10);
        return view('tenant.acordos.index', compact('acordos'));
    }

    public function create(Request $request)
    {
        $devedorId = $request->get('devedor');
        $devedor = Devedor::with(['titulos' => function($q) {
            $q->where('status', 'aberto');
        }])->findOrFail($devedorId);

        // Somar débitos
        $totalOriginal = $devedor->titulos->sum('valor_original');
        
        return view('tenant.acordos.create', compact('devedor', 'totalOriginal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'valor_original' => 'required|numeric',
            'desconto' => 'required|numeric|min:0',
            'valor_acordo' => 'required|numeric',
            'entrada' => 'required|numeric|min:0',
            'parcelas' => 'required|integer|min:1|max:48',
            'vencimento_primeira' => 'required|date|after_or_equal:today',
        ]);

        return DB::transaction(function () use ($request) {
            $devedor = Devedor::findOrFail($request->devedor_id);

            // 1. Criar Acordo
            $acordo = Acordo::create([
                'tenant_id' => auth()->user()->tenant_id,
                'devedor_id' => $devedor->id,
                'valor_original' => $request->valor_original,
                'desconto' => $request->desconto,
                'valor_acordo' => $request->valor_acordo,
                'entrada' => $request->entrada,
                'parcelas' => $request->parcelas,
                'status' => 'ativo',
            ]);

            // 2. Marcar títulos como "cancelados/em_acordo" (substituídos pelo acordo)
            Titulo::where('devedor_id', $devedor->id)
                ->where('status', 'aberto')
                ->update(['status' => 'cancelado']);

            // 3. Gerar Parcelas
            $valorParcela = ($request->valor_acordo - $request->entrada) / $request->parcelas;
            $dataVencimento = Carbon::parse($request->vencimento_primeira);

            for ($i = 1; $i <= $request->parcelas; $i++) {
                AcordoParcela::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'acordo_id' => $acordo->id,
                    'numero' => $i,
                    'valor' => $valorParcela,
                    'vencimento' => $dataVencimento->copy()->addMonths($i - 1),
                    'status' => 'aberto',
                ]);
            }

            return redirect()->route('tenant.devedores.show', $devedor->id)->with('success', 'Acordo formalizado com sucesso! Títulos originais foram baixados.');
        });
    }

    public function show(Acordo $acordo)
    {
        $acordo->load(['devedor', 'acordoParcelas', 'pagamentos']);
        return view('tenant.acordos.show', compact('acordo'));
    }
}
