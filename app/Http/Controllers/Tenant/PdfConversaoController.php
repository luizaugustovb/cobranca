<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\CloudConvertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfConversaoController extends Controller
{
    public function create()
    {
        return view('tenant.pdf-conversao.create');
    }

    public function store(Request $request, CloudConvertService $cloudConvert)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf|max:20480',
        ], [
            'arquivo.required' => 'Selecione um arquivo PDF.',
            'arquivo.mimes'    => 'Somente arquivos PDF são aceitos.',
            'arquivo.max'      => 'O arquivo não pode ultrapassar 20 MB.',
        ]);

        $file     = $request->file('arquivo');
        $pdfPath  = $file->getRealPath();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        try {
            $xlsxContent = $cloudConvert->convertToXlsx($pdfPath, $file->getClientOriginalName());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['arquivo' => 'Erro na conversão: ' . $e->getMessage()]);
        }

        // Salva o XLSX temporariamente e oferece para download
        $xlsxName = $baseName . '_convertido.xlsx';
        $xlsxPath = 'conversoes/' . $xlsxName;

        Storage::put($xlsxPath, $xlsxContent);

        return response()->streamDownload(
            fn () => print($xlsxContent),
            $xlsxName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
