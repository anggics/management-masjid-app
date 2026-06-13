@extends('layouts.app')
@section('title', 'Tabungan Qurban')

@section('content')
    <div class="flex items-center justify-between mb-3">
        <h1 class="page-title">Peserta Qurban</h1>
        @auth
            <a href="{{ route('user.qurban') }}" class="btn-primary px-4 py-2 text-sm">Tabungan Saya</a>
        @else
            <a href="{{ route('login') }}" class="btn-primary px-4 py-2 text-sm">Daftar Qurban</a>
        @endauth
    </div>

    @forelse($participants as $p)
        <div class="card mb-3">
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-semibold">{{ $p->name }}</div>
                    <div class="text-sm text-muted capitalize">{{ $p->qurbanType?->label() ?? $p->animal_type }}</div>
                </div>
                <span class="badge-{{ $p->status === 'completed' ? 'verified' : 'pending' }} capitalize">{{ $p->status }}</span>
            </div>
            <div class="mt-3">
                <div class="h-2 bg-surface-2 rounded-full overflow-hidden">
                    <div class="h-full bg-accent" style="width: {{ $p->progressPercent() }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-muted mt-1">
                    <span>Rp {{ number_format($p->collected_amount,0,',','.') }}</span>
                    <span>dari Rp {{ number_format($p->target_amount,0,',','.') }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <span class="empty-state-icon">🐐</span>
            <p class="font-semibold text-ink">Belum ada peserta qurban</p>
            <p class="text-sm">Jadilah yang pertama mendaftar tabungan qurban.</p>
        </div>
    @endforelse

    <div class="mt-3">{{ $participants->links() }}</div>
@endsection
