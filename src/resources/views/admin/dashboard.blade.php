@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card"><div class="text-xs text-muted">Total Jamaah</div><div class="text-2xl font-bold">{{ $stats['jamaah'] }}</div></div>
        <div class="card"><div class="text-xs text-muted">Peserta Qurban</div><div class="text-2xl font-bold">{{ $stats['qurban'] }}</div></div>
        <div class="card"><div class="text-xs text-muted">Setoran Pending</div><div class="text-2xl font-bold text-amber-600">{{ $stats['pending_deposits'] }}</div></div>
        <div class="card"><div class="text-xs text-muted">Saldo Kas</div><div class="text-lg font-bold text-primary">Rp {{ number_format($stats['balance'],0,',','.') }}</div></div>
    </div>

    <h2 class="font-bold mb-2">Setoran Menunggu Verifikasi</h2>
    <div class="card">
        @forelse($pending as $d)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <div class="font-semibold">{{ $d->user?->name ?? '—' }} • {{ $d->participant?->name }}</div>
                    <div class="text-sm text-muted">Rp {{ number_format($d->amount,0,',','.') }} • {{ $d->created_at->diffForHumans() }}</div>
                </div>
                <a href="{{ route('admin.deposits.index') }}" class="btn-secondary px-3 py-1.5 text-sm">Tinjau</a>
            </div>
        @empty
            <p class="text-muted text-sm">Tidak ada setoran pending. 🎉</p>
        @endforelse
    </div>
@endsection
