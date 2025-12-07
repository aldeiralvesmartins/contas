<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Transações - {{ $selected_month_name }}</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji'; color:#0f172a; }
        .container { max-width: 960px; margin: 0 auto; padding: 24px; }
        .header { display:flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: 700; }
        .subtitle { color:#475569; font-size: 14px; }
        .cards { display:flex; gap:12px; margin: 16px 0 24px; }
        .card { flex:1; border:1px solid #e2e8f0; border-radius:12px; padding:12px; }
        .label { font-size:12px; color:#64748b; margin-bottom:6px; }
        .value { font-weight:700; font-size:18px; }
        .income { color:#059669; }
        .expense { color:#dc2626; }
        .balance { color:#0f172a; }
        table { width:100%; border-collapse: collapse; font-size: 13px; }
        th, td { border-bottom:1px solid #e2e8f0; padding:10px 8px; text-align:left; }
        th { background:#f8fafc; font-weight:600; color:#334155; }
        .tag { display:inline-block; padding:2px 8px; border-radius:9999px; font-size:11px; font-weight:600; }
        .tag-income { background:#ecfdf5; color:#047857; }
        .tag-expense { background:#fef2f2; color:#b91c1c; }
        .muted { color:#64748b; }
        .footer { margin-top:16px; font-size:12px; color:#64748b; }
        @media print { .no-print { display: none; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
        .actions { margin-bottom: 12px; }
        .btn { padding:8px 12px; border-radius:8px; background:#10b981; color:white; text-decoration:none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="title">Relatório de Transações</div>
                <div class="subtitle">Período: {{ $selected_month_name }} ({{ $selected_month }})</div>
            </div>
            <div class="no-print actions">
                <a class="btn" href="javascript:window.print()">Imprimir / Salvar PDF</a>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="label">Entradas</div>
                <div class="value income">R$ {{ number_format($total_income, 2, ',', '.') }}</div>
            </div>
            <div class="card">
                <div class="label">Saídas</div>
                <div class="value expense">R$ {{ number_format($total_expense, 2, ',', '.') }}</div>
            </div>
            <div class="card">
                <div class="label">Saldo</div>
                <div class="value balance">R$ {{ number_format($balance, 2, ',', '.') }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th style="text-align:right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr>
                        <td class="muted">{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}</td>
                        <td>{{ $t->description }}</td>
                        <td class="muted">{{ $t->category->name }}</td>
                        <td>
                            <span class="tag {{ $t->type === 'income' ? 'tag-income' : 'tag-expense' }}">{{ $t->type === 'income' ? 'Entrada' : 'Saída' }}</span>
                        </td>
                        <td class="muted">{{ $t->status === 'paid' ? 'Pago' : 'Pendente' }}</td>
                        <td style="text-align:right" class="{{ $t->type === 'income' ? 'income' : 'expense' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}R$ {{ number_format($t->amount, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">Sem transações neste período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">Gerado em {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</body>
</html>
