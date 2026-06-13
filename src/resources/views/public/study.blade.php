@extends('layouts.app')
@section('title', 'Jadwal Kajian')

@section('content')
    <h1 class="page-title mb-3">Jadwal Kajian</h1>

    <div class="flex gap-2 mb-4">
        <a href="{{ route('study.public', ['filter' => 'upcoming']) }}" class="pill {{ $filter !== 'past' ? 'pill-active' : 'pill-idle' }}">Mendatang</a>
        <a href="{{ route('study.public', ['filter' => 'past']) }}" class="pill {{ $filter === 'past' ? 'pill-active' : 'pill-idle' }}">Selesai</a>
    </div>

    @forelse($schedules as $k)
        <a href="{{ route('study.show', $k) }}" class="card flex gap-3 mb-3 hover:shadow-md active:scale-[.99] transition">
            @if($k->poster_url)
                <img src="{{ $k->poster_url }}" class="w-16 h-16 rounded-xl object-cover" alt="poster">
            @else
                <div class="w-16 h-16 rounded-xl bg-surface-2 flex items-center justify-center text-2xl">📖</div>
            @endif
            <div class="flex-1">
                <div class="font-semibold">{{ $k->title }}</div>
                <div class="text-sm text-muted">{{ $k->speaker }}</div>
                <div class="text-sm text-muted">{{ $k->scheduled_at->translatedFormat('d M Y, H:i') }} • {{ $k->location }}</div>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <span class="empty-state-icon">📚</span>
            <p class="font-semibold text-ink">Tidak ada kajian</p>
            <p class="text-sm">Belum ada jadwal kajian untuk filter ini.</p>
        </div>
    @endforelse

    <div class="mt-3">{{ $schedules->links() }}</div>
@endsection
