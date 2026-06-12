<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1A1A2E; }
        h1 { color: #1B4332; margin-bottom: 0; }
        .muted { color: #6B7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #1B4332; color: #fff; }
        .right { text-align: right; }
        .summary td { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan — {{ $mosque->name }}</h1>
    <p class="muted">Periode: {{ \Carbon\Carbon::create()->month($period['month'])->translatedFormat('F') }} {{ $period['year'] }}</p>

    <table>
        <thead><tr><th>Tanggal</th><th>Kategori</th><th>Tipe</th><th class="right">Jumlah (Rp)</th></tr></thead>
        <tbody>
            @foreach($records as $r)
                <tr>
                    <td>{{ $r->recorded_at->format('d-m-Y') }}</td>
                    <td>{{ $r->category }}</td>
                    <td>{{ $r->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td class="right">{{ number_format($r->amount,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="summary">
            <tr><td colspan="3">Total Pemasukan</td><td class="right">{{ number_format($summary['income'],0,',','.') }}</td></tr>
            <tr><td colspan="3">Total Pengeluaran</td><td class="right">{{ number_format($summary['expense'],0,',','.') }}</td></tr>
            <tr><td colspan="3">Saldo</td><td class="right">{{ number_format($summary['balance'],0,',','.') }}</td></tr>
        </tfoot>
    </table>
</body>
</html>
