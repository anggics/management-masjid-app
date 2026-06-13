@extends('layouts.app')
@section('title', 'Laporan Keuangan')

@section('content')
    <h1 class="page-title mb-1">Laporan Keuangan</h1>
    <p class="page-subtitle mb-4">Transparansi kas {{ $mosque->name }}.</p>

    {{-- Saldo kas keseluruhan (semua waktu, tanpa filter periode) --}}
    <div class="card mb-4 bg-gradient-to-br from-primary to-primary-light text-white border-0 text-center">
        <div class="text-xs opacity-80">Saldo Kas Keseluruhan</div>
        <div class="text-3xl font-bold">Rp {{ number_format($totalBalance,0,',','.') }}</div>
        <div class="text-xs opacity-80">Total seluruh pemasukan dikurangi pengeluaran</div>
    </div>

    <form method="GET" class="flex gap-2 mb-4">
        <select name="month" class="input">
            @foreach(range(1,12) as $mo)
                <option value="{{ $mo }}" @selected($mo === $month)>{{ \Carbon\Carbon::create()->month($mo)->translatedFormat('F') }}</option>
            @endforeach
        </select>
        <select name="year" class="input">
            @foreach(range(now()->year, now()->year-4) as $yr)
                <option value="{{ $yr }}" @selected($yr === $year)>{{ $yr }}</option>
            @endforeach
        </select>
        <button class="btn-primary">Filter</button>
    </form>

    <div class="grid grid-cols-3 gap-3 mb-5">
        <div class="card text-center"><div class="text-xs text-muted">Pemasukan</div><div class="font-bold text-green-700 text-sm">Rp {{ number_format($summary['income'],0,',','.') }}</div></div>
        <div class="card text-center"><div class="text-xs text-muted">Pengeluaran</div><div class="font-bold text-red-600 text-sm">Rp {{ number_format($summary['expense'],0,',','.') }}</div></div>
        <div class="card text-center"><div class="text-xs text-muted">Saldo</div><div class="font-bold text-primary text-sm">Rp {{ number_format($summary['balance'],0,',','.') }}</div></div>
    </div>

    <div class="card">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-muted border-b"><th class="py-2">Tanggal</th><th>Kategori</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @forelse($records as $r)
                    <tr class="border-b last:border-0">
                        <td class="py-2">{{ $r->recorded_at->translatedFormat('d M') }}</td>
                        <td>{{ $r->category }}</td>
                        <td class="text-right font-semibold {{ $r->type === 'income' ? 'text-green-700' : 'text-red-600' }}">
                            {{ $r->type === 'income' ? '+' : '−' }} {{ number_format($r->amount,0,',','.') }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-muted">Belum ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $records->links() }}</div>
    </div>
@endsection
