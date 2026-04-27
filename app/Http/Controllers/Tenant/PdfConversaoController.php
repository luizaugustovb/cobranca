<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\PdfExtractorService;
use App\Services\ImportacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfConversaoController extends Controller
{
    public function create()
    {
        return view('tenant.pdf-conversao.create');
    }

    /**
     * Modo: Converter PDF → download XLSX
     */
    public function store(Request $request, PdfExtractorService $extractor)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf|max:20480',
            'engine'  => 'in:pdfplumber,tabula',
        ], [
            'arquivo.required' => 'Selecione um arquivo PDF.',
            'arquivo.mimes'    => 'Somente arquivos PDF são aceitos.',
            'arquivo.max'      => 'O arquivo não pode ultrapassar 20 MB.',
        ]);

        $file     = $request->file('arquivo');
        $pdfPath  = $file->getRealPath();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $engine   = $request->input('engine', 'pdfplumber');

        try {
            $xlsxContent = $extractor->toXlsx($pdfPath, $engine);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['arquivo' => $e->getMessage()]);
        }

        $xlsxName = $baseName . '_convertido.xlsx';

        return response()->streamDownload(
            fn() => print($xlsxContent),
            $xlsxName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    /**
     * Modo: Leitura direta — extrai tabelas e retorna preview sem converter
     */
    public function preview(Request $request, PdfExtractorService $extractor)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf|max:20480',
            'engine'  => 'in:pdfplumber,tabula',
        ], [
            'arquivo.required' => 'Selecione um arquivo PDF.',
            'arquivo.mimes'    => 'Somente arquivos PDF são aceitos.',
            'arquivo.max'      => 'O arquivo não pode ultrapassar 20 MB.',
        ]);

        $file   = $request->file('arquivo');
        $engine = $request->input('engine', 'pdfplumber');

        try {
            $tables = $extractor->extract($file->getRealPath(), $engine);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['arquivo' => $e->getMessage()]);
        }

        // Salva tabelas em arquivo temporário para importação posterior
        $key  = 'pdf_preview_' . auth()->id() . '_' . time();
        Storage::put("temp/{$key}.json", json_encode($tables));

        return view('tenant.pdf-conversao.preview', compact('tables', 'key', 'engine'));
    }

    /**
     * Modo: Importar diretamente os dados extraídos do PDF
     */
    public function importar(Request $request, ImportacaoService $importService)
    {
        $request->validate(['key' => 'required|string']);

        $key  = preg_replace('/[^a-z0-9_]/', '', $request->input('key'));
        $path = "temp/{$key}.json";

        if (!Storage::exists($path)) {
            return back()->withErrors(['key' => 'Sessão de preview expirada. Envie o PDF novamente.']);
        }

        $tables = json_decode(Storage::get($path), true);
        Storage::delete($path);

        if (empty($tables)) {
            return back()->withErrors(['key' => 'Nenhuma tabela encontrada para importar.']);
        }

        // Usa a primeira tabela (com mais colunas) como base
        $mainTable = collect($tables)->sortByDesc(fn($t) => count($t[0] ?? []))->first();

        // Converte para CSV e salva para o ImportacaoService processar
        $csv  = '';
        foreach ($mainTable as $row) {
            $csv .= implode(';', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        $csvPath = 'imports/pdf_direto_' . auth()->id() . '_' . time() . '.csv';
        Storage::put($csvPath, $csv);

        $importacao = $importService->create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id'   => auth()->id(),
            'arquivo'   => $csvPath,
            'tipo'      => 'cobrancas',
        ]);

        [$importados, $erros, $erroDetalhe] = $importService->process($importacao);

        $msg = "Importação concluída: {$importados} título(s) processado(s)";
        if ($erros) {
            $msg .= " | {$erros} linha(s) com erro.";
        }

        return redirect()->route('tenant.importacoes')->with('success', $msg);
    }
}
