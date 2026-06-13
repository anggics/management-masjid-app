@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card flex items-center gap-3">
            <span class="w-11 h-11 rounded-xl bg-surface-2 flex items-center justify-center text-xl shrink-0">🧑‍🤝‍🧑</span>
            <div class="min-w-0"><div class="text-xs text-muted">Total Jamaah</div><div class="text-2xl font-bold leading-tight">{{ $stats['jamaah'] }}</div></div>
        </div>
        <div class="card flex items-center gap-3">
            <span class="w-11 h-11 rounded-xl bg-surface-2 flex items-center justify-center text-xl shrink-0">🐐</span>
            <div class="min-w-0"><div class="text-xs text-muted">Peserta Qurban</div><div class="text-2xl font-bold leading-tight">{{ $stats['qurban'] }}</div></div>
        </div>
        <div class="card flex items-center gap-3">
            <span class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-xl shrink-0">⏳</span>
            <div class="min-w-0"><div class="text-xs text-muted">Setoran Pending</div><div class="text-2xl font-bold text-amber-600 leading-tight">{{ $stats['pending_deposits'] }}</div></div>
        </div>
        <div class="card flex items-center gap-3">
            <span class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-xl shrink-0">💰</span>
            <div class="min-w-0"><div class="text-xs text-muted">Saldo Kas</div><div class="text-lg font-bold text-primary leading-tight">Rp {{ number_format($stats['balance'],0,',','.') }}</div></div>
        </div>
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
            <div class="flex flex-col items-center justify-center text-center text-muted py-8 gap-2">
                <span class="w-14 h-14 rounded-full bg-surface-2 flex items-center justify-center text-2xl">🎉</span>
                <p class="font-semibold text-ink">Semua beres</p>
                <p class="text-sm">Tidak ada setoran menunggu verifikasi.</p>
            </div>
        @endforelse
    </div>
@endsection
