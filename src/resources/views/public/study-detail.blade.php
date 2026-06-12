@extends('layouts.app')
@section('title', $study->title)

@section('content')
    <a href="{{ route('study.public') }}" class="text-sm text-accent font-semibold">← Kembali</a>

    <div class="card mt-3">
        @if($study->poster_url)
            <img src="{{ $study->poster_url }}" class="w-full rounded-xl mb-3" alt="poster">
        @endif
        <h1 class="text-xl font-bold">{{ $study->title }}</h1>
        <span class="badge-{{ $study->status === 'cancelled' ? 'rejected' : ($study->status === 'done' ? 'verified' : 'pending') }} mt-1 inline-block">{{ ucfirst($study->status) }}</span>

        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-muted">Pemateri</dt><dd class="font-semibold">{{ $study->speaker }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">Waktu</dt><dd class="font-semibold">{{ $study->scheduled_at->translatedFormat('l, d F Y • H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-muted">Lokasi</dt><dd class="font-semibold">{{ $study->location }}</dd></div>
        </dl>

        @if($study->description)
            <p class="mt-4 text-sm leading-relaxed">{{ $study->description }}</p>
        @endif
    </div>
@endsection
