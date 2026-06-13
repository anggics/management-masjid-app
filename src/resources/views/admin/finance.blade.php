@extends('layouts.admin')
@section('title', 'Laporan Keuangan')

@section('content')
    <form method="GET" class="flex flex-wrap gap-2 mb-4 items-end">
        <div><label class="label">Bulan</label>
            <select name="month" class="input">@foreach(range(1,12) as $mo)<option value="{{ $mo }}" @selected($mo === $period['month'])>{{ \Carbon\Carbon::create()->month($mo)->translatedFormat('F') }}</option>@endforeach</select>
        </div>
        <div><label class="label">Tahun</label>
            <select name="year" class="input">@foreach(range(now()->year, now()->year-4) as $yr)<option value="{{ $yr }}" @selected($yr === $period['year'])>{{ $yr }}</option>@endforeach</select>
        </div>
        <button class="btn-primary">Filter</button>
        <a href="{{ route('admin.finance.export', $period) }}" class="btn-secondary">⬇ Export PDF</a>
    </form>

    {{-- Saldo kas keseluruhan (semua waktu, tanpa filter tanggal) --}}
    <div class="card mb-4 bg-primary text-white text-center">
        <div class="text-xs opacity-80">Saldo Kas Keseluruhan</div>
        <div class="text-2xl font-bold">Rp {{ number_format($totalBalance,0,',','.') }}</div>
        <div class="text-xs opacity-80">Total seluruh pemasukan dikurangi pengeluaran (tanpa melihat periode)</div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="card text-center"><div class="text-xs text-muted">Pemasukan (periode)</div><div class="font-bold text-green-700">Rp {{ number_format($summary['income'],0,',','.') }}</div></div>
        <div class="card text-center"><div class="text-xs text-muted">Pengeluaran (periode)</div><div class="font-bold text-red-600">Rp {{ number_format($summary['expense'],0,',','.') }}</div></div>
        <div class="card text-center"><div class="text-xs text-muted">Saldo (periode)</div><div class="font-bold text-primary">Rp {{ number_format($summary['balance'],0,',','.') }}</div></div>
    </div>

    <details class="card mb-4">
        <summary class="font-semibold cursor-pointer">+ Tambah catatan keuangan</summary>
        <form method="POST" action="{{ route('admin.finance.store') }}" class="mt-3 grid md:grid-cols-2 gap-3">
            @csrf
            <div><label class="label">Tipe</label><select name="type" class="input"><option value="income">Pemasukan</option><option value="expense">Pengeluaran</option></select></div>
            <div><label class="label">Kategori</label><input name="category" class="input" placeholder="mis. Infaq Jumat" required></div>
            <div><label class="label">Jumlah (Rp)</label><input type="number" name="amount" class="input" required></div>
            <div><label class="label">Tanggal</label><input type="date" name="recorded_at" value="{{ now()->toDateString() }}" class="input" required></div>
            <div class="md:col-span-2"><label class="label">Keterangan</label><input name="description" class="input"></div>
            <div class="md:col-span-2"><button class="btn-primary">Simpan</button></div>
        </form>
    </details>

    <div class="card overflow-x-auto">
        <table class="table-admin">
            <thead><tr><th class="py-2">Tanggal</th><th>Kategori</th><th>Tipe</th><th class="text-right">Jumlah</th>@if(auth()->user()->isAdmin())<th></th>@endif</tr></thead>
            <tbody>
                @forelse($records as $r)
                    <tr class="border-b last:border-0" x-data="{ edit: false }">
                        <td class="py-2">{{ $r->recorded_at->translatedFormat('d M Y') }}</td>
                        <td>{{ $r->category }}</td>
                        <td><span class="badge-{{ $r->type === 'income' ? 'verified' : 'rejected' }}">{{ $r->type === 'income' ? 'Masuk' : 'Keluar' }}</span></td>
                        <td class="text-right font-semibold {{ $r->type === 'income' ? 'text-green-700' : 'text-red-600' }}">Rp {{ number_format($r->amount,0,',','.') }}</td>
                        @if(auth()->user()->isAdmin())
                            <td class="text-right whitespace-nowrap">
                                <button class="text-primary mr-2" @click="edit = true">Edit</button>
                                <form method="POST" action="{{ route('admin.finance.destroy', $r) }}" class="inline" onsubmit="return confirm('Hapus catatan ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>

                                <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                                    <form method="POST" action="{{ route('admin.finance.update', $r) }}" class="bg-white rounded-2xl shadow-xl p-5 w-full max-w-md space-y-3 text-left">
                                        @csrf @method('PUT')
                                        <h3 class="font-bold">Edit Catatan Keuangan</h3>
                                        <div>
                                            <label class="label">Tipe</label>
                                            <select name="type" class="input">
                                                <option value="income" @selected($r->type === 'income')>Pemasukan</option>
                                                <option value="expense" @selected($r->type === 'expense')>Pengeluaran</option>
                                            </select>
                                        </div>
                                        <div><label class="label">Kategori</label><input name="category" class="input" value="{{ $r->category }}" required></div>
                                        <div><label class="label">Jumlah (Rp)</label><input type="number" name="amount" class="input" value="{{ (int) $r->amount }}" required></div>
                                        <div><label class="label">Tanggal</label><input type="date" name="recorded_at" class="input" value="{{ $r->recorded_at->toDateString() }}" required></div>
                                        <div><label class="label">Keterangan</label><input name="description" class="input" value="{{ $r->description }}"></div>
                                        <div class="flex gap-2 justify-end">
                                            <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                                            <button class="btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-muted">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $records->links() }}</div>
@endsection
