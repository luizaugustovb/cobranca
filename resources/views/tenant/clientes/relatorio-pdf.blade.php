<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorio de Pagamentos - {{ $cliente->nome }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 24px 32px 16px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .company {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .company-sub {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 2px;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            color: #334155;
        }

        .report-title p {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .info-box {
            display: flex;
            gap: 24px;
            padding: 16px 32px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .info-item {}

        .info-label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
        }

        .info-value {
            font-size: 13px;
            font-weight: 900;
            color: #1e293b;
        }

        .summary {
            display: flex;
            gap: 16px;
            padding: 0 32px 24px;
        }

        .card {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .card-label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
        }

        .card-value {
            font-size: 20px;
            font-weight: 900;
            margin-top: 4px;
        }

        .card-value.green {
            color: #059669;
        }

        .card-value.blue {
            color: #2563eb;
        }

        .card-value.slate {
            color: #334155;
        }

        .section {
            padding: 0 32px 24px;
            page-break-inside: avoid;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
            border-radius: 8px 8px 0 0;
            padding: 10px 16px;
        }

        .devedor-name {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.3px;
        }

        .devedor-doc {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
        }

        .devedor-total {
            font-size: 13px;
            font-weight: 900;
            color: #059669;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        thead th {
            padding: 8px 12px;
            text-align: left;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        thead th.right {
            text-align: right;
        }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }

        tbody td.right {
            text-align: right;
            font-weight: 900;
            color: #059669;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            background: #ecfdf5;
        }

        .total-row td {
            font-weight: 900;
        }

        .footer {
            padding: 24px 32px 16px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="background:#1e293b;color:#fff;padding:10px 32px;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:11px;font-weight:700;">Visualizacao para impressao / PDF</span>
        <button onclick="window.print()" style="background:#059669;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:900;font-size:12px;cursor:pointer;text-transform:uppercase;letter-spacing:1px;">
            Imprimir / Salvar PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="company">{{ $settings['company_name'] ?? 'Escritorio de Cobranças' }}</div>
            <div class="company-sub">Sistema de Cobranca</div>
        </div>
        <div class="report-title">
            <h1>Relatorio de Pagamentos</h1>
            <p>Gerado em {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="info-box">
        <div class="info-item">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $cliente->nome }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Documento</div>
            <div class="info-value">{{ $cliente->documento }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Periodo</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</div>
        </div>
        @if($cliente->email)
        <div class="info-item">
            <div class="info-label">E-mail</div>
            <div class="info-value">{{ $cliente->email }}</div>
        </div>
        @endif
    </div>

    <div class="summary">
        <div class="card">
            <div class="card-label">Total Recebido</div>
            <div class="card-value green">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Devedores que Pagaram</div>
            <div class="card-value blue">{{ $porDevedor->count() }}</div>
        </div>
        <div class="card">
            <div class="card-label">Transacoes</div>
            <div class="card-value slate">{{ $porDevedor->sum(fn($i) => $i['pagamentos']->count()) }}</div>
        </div>
    </div>

    @forelse($porDevedor as $item)
    <div class="section">
        <div class="section-header">
            <div>
                <div class="devedor-name">{{ $item['devedor']->nome ?? 'Devedor removido' }}</div>
                @if($item['devedor'])
                <div class="devedor-doc">{{ $item['devedor']->cpf_cnpj }}</div>
                @endif
            </div>
            <div class="devedor-total">R$ {{ number_format($item['total'], 2, ',', '.') }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Acordo</th>
                    <th>Forma de Pagamento</th>
                    <th class="right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item['pagamentos'] as $pag)
                <tr>
                    <td>{{ $pag->data_pagamento->format('d/m/Y') }}</td>
                    <td>#{{ $pag->acordo_id }}</td>
                    <td>{{ $pag->forma_pagamento ?? '-' }}</td>
                    <td class="right">R$ {{ number_format($pag->valor, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="font-size:10px;color:#059669;text-transform:uppercase;letter-spacing:1px;">Subtotal</td>
                    <td class="right">R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:1px;">
        Nenhum pagamento encontrado neste periodo.
    </div>
    @endforelse

    <div class="footer">
        <span>{{ $settings['company_name'] ?? 'Sistema de Cobranca' }} &bull; Relatorio gerado automaticamente</span>
        <span>Total geral: R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
    </div>
</body>

</html>