<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Extrai tabelas de PDFs usando script Python local (pdfplumber ou tabula).
 *
 * Modos:
 *   toXlsx()  — converte para XLSX e retorna conteúdo binário
 *   extract() — retorna array de tabelas para uso direto (importação)
 */
class PdfExtractorService
{
    private string $pythonBin;
    private string $scriptPath;

    public function __construct()
    {
        // Detecta SO: Windows usa Scripts\python.exe, Linux/Mac usa bin/python3
        $venvBin = PHP_OS_FAMILY === 'Windows'
            ? base_path('.venv/Scripts/python.exe')
            : base_path('.venv/bin/python3');

        $this->pythonBin  = file_exists($venvBin) ? $venvBin : (PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
        $this->scriptPath = base_path('scripts/pdf_extractor.py');
    }

    /**
     * Extrai tabelas do PDF e retorna array multidimensional.
     *
     * @param  string  $pdfPath  Caminho absoluto do PDF
     * @param  string  $engine   'pdfplumber' ou 'tabula'
     * @return array  [ [['col1','col2'], ['val1','val2']], ... ]
     *
     * @throws \RuntimeException
     */
    public function extract(string $pdfPath, string $engine = 'pdfplumber'): array
    {
        $engine = in_array($engine, ['pdfplumber', 'tabula']) ? $engine : 'pdfplumber';

        $cmd = sprintf(
            '%s %s --pdf %s --engine %s',
            escapeshellarg($this->pythonBin),
            escapeshellarg($this->scriptPath),
            escapeshellarg($pdfPath),
            escapeshellarg($engine)
        );

        $output    = [];
        $exitCode  = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        $json   = implode('', $output);
        $result = json_decode($json, true);

        if (!$result || !($result['success'] ?? false)) {
            throw new \RuntimeException(
                'Falha na extração do PDF: ' . ($result['error'] ?? $json)
            );
        }

        if (empty($result['tables'])) {
            throw new \RuntimeException(
                'Nenhuma tabela encontrada no PDF com a engine "' . $engine . '". Tente a outra engine.'
            );
        }

        return $result['tables'];
    }

    /**
     * Extrai tabelas e gera conteúdo binário XLSX.
     *
     * @throws \RuntimeException
     */
    public function toXlsx(string $pdfPath, string $engine = 'pdfplumber'): string
    {
        $tables = $this->extract($pdfPath, $engine);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // remove aba padrão

        foreach ($tables as $index => $table) {
            $sheet = $spreadsheet->createSheet($index);
            $sheet->setTitle('Tabela ' . ($index + 1));

            foreach ($table as $rowIndex => $row) {
                foreach ($row as $colIndex => $cell) {
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $cell);
                }
            }

            // Negrito na primeira linha (cabeçalho)
            if (!empty($table)) {
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($table[0]));
                $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

                // Auto-largura nas colunas
                foreach (range(1, count($table[0])) as $col) {
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                }
            }
        }

        if ($spreadsheet->getSheetCount() === 0) {
            throw new \RuntimeException('Nenhuma tabela gerada para o XLSX.');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
