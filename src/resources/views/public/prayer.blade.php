@extends('layouts.app')
@section('title', 'Jadwal Sholat')

@section('content')
    <h1 class="text-xl font-bold mb-1">Jadwal Sholat</h1>
    <p class="text-muted text-sm mb-4">{{ $mosque->city }} • Metode {{ $mosque->prayer_method }}</p>

    <div class="card mb-4">
        <div class="flex items-center justify-between mb-3">
            <a href="{{ route('prayer.index', ['date' => $prev]) }}" class="btn-secondary px-3 py-1.5 text-sm">‹ Sebelumnya</a>
            <div class="text-center font-semibold">{{ $date->translatedFormat('l, d F Y') }}</div>
            <a href="{{ route('prayer.index', ['date' => $next]) }}" class="btn-secondary px-3 py-1.5 text-sm">Berikutnya ›</a>
        </div>

        @if($schedule['source'] === 'override')
            <p class="text-xs text-amber-700 mb-2">⚙ Jadwal disesuaikan manual oleh pengurus.</p>
        @endif

        <div class="grid grid-cols-2 gap-3">
            @forelse($schedule['times'] as $name => $time)
                <div class="flex items-center justify-between rounded-xl bg-surface-2 px-4 py-3">
                    <span class="font-semibold">{{ $name }}</span>
                    <span class="font-mono text-lg">{{ $time }}</span>
                </div>
            @empty
                <p class="col-span-2 text-muted text-sm">{{ $schedule['note'] ?? 'Jadwal tidak tersedia.' }}</p>
            @endforelse
        </div>
    </div>
@endsection
