<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Converte PDF em XLSX usando a API CloudConvert.
 *
 * Fluxo:
 *   1. POST /jobs — cria job com 2 tasks: import/upload + convert/pdf-xlsx
 *   2. PUT /import/{task} — faz upload do arquivo
 *   3. GET /jobs/{id} — aguarda taskSuccess
 *   4. GET /export URL — baixa o XLSX
 */
class CloudConvertService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.cloudconvert.com/v2';

    public function __construct()
    {
        $apiKey = config('services.cloudconvert.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException('A chave da API do CloudConvert não está configurada. Defina CLOUDCONVERT_API_KEY no arquivo .env.');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Converte um arquivo PDF e retorna o conteúdo binário do XLSX.
     *
     * @param  string  $pdfPath  Caminho absoluto do arquivo PDF no servidor
     * @param  string  $originalName  Nome original do arquivo (para log)
     * @throws \RuntimeException
     */
    public function convertToXlsx(string $pdfPath, string $originalName = 'file.pdf'): string
    {
        // 1. Cria o job com 3 tasks: upload → convert → export
        $jobResponse = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/jobs", [
                'tasks' => [
                    'upload-file' => [
                        'operation' => 'import/upload',
                    ],
                    'convert-file' => [
                        'operation'     => 'convert',
                        'input'         => 'upload-file',
                        'input_format'  => 'pdf',
                        'output_format' => 'xlsx',
                    ],
                    'export-file' => [
                        'operation' => 'export/url',
                        'input'     => 'convert-file',
                    ],
                ],
            ]);

        if (!$jobResponse->successful()) {
            throw new \RuntimeException('CloudConvert: falha ao criar job — ' . $jobResponse->body());
        }

        $job   = $jobResponse->json();
        $jobId = $job['data']['id'];

        // Localiza a task de upload para obter a URL e formulário
        $uploadTask = collect($job['data']['tasks'])->firstWhere('name', 'upload-file');

        if (!$uploadTask || empty($uploadTask['result']['form']['url'])) {
            throw new \RuntimeException('CloudConvert: task de upload não encontrada na resposta.');
        }

        $uploadUrl    = $uploadTask['result']['form']['url'];
        $uploadParams = $uploadTask['result']['form']['parameters'] ?? [];

        // 2. Faz upload do arquivo via multipart form
        $http = Http::withToken($this->apiKey)->asMultipart();

        // Adiciona os campos do formulário S3
        foreach ($uploadParams as $key => $value) {
            $http = $http->attach($key, $value);
        }

        $uploadResponse = $http
            ->attach('file', file_get_contents($pdfPath), basename($pdfPath))
            ->post($uploadUrl);

        // Upload para S3 retorna 204 em sucesso
        if (!in_array($uploadResponse->status(), [200, 201, 204])) {
            throw new \RuntimeException('CloudConvert: falha no upload do arquivo — HTTP ' . $uploadResponse->status());
        }

        // 3. Aguarda conclusão do job (polling com timeout de 120s)
        $timeout  = 120;
        $interval = 3;
        $elapsed  = 0;
        $xlsxUrl  = null;

        while ($elapsed < $timeout) {
            sleep($interval);
            $elapsed += $interval;

            $statusResponse = Http::withToken($this->apiKey)
                ->get("{$this->baseUrl}/jobs/{$jobId}");

            if (!$statusResponse->successful()) {
                continue;
            }

            $jobData = $statusResponse->json('data');

            if ($jobData['status'] === 'error') {
                throw new \RuntimeException('CloudConvert: job encerrou com erro — ' . json_encode($jobData));
            }

            if ($jobData['status'] === 'finished') {
                $exportTask = collect($jobData['tasks'])->firstWhere('name', 'export-file');
                $xlsxUrl    = $exportTask['result']['files'][0]['url'] ?? null;
                break;
            }
        }

        if (!$xlsxUrl) {
            throw new \RuntimeException('CloudConvert: timeout aguardando conversão do arquivo.');
        }

        // 4. Baixa o XLSX convertido
        $download = Http::withToken($this->apiKey)->get($xlsxUrl);

        if (!$download->successful()) {
            throw new \RuntimeException('CloudConvert: falha ao baixar o XLSX convertido.');
        }

        Log::info("CloudConvert: conversão concluída para [{$originalName}]");

        return $download->body();
    }
}
