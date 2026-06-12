@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
    <h1 class="text-xl font-bold mb-3">Notifikasi</h1>

    @forelse($notifications as $n)
        <div class="card mb-3 {{ $n->read_at ? 'opacity-70' : '' }}">
            <div class="flex justify-between items-start gap-3">
                <div>
                    <div class="font-semibold">{{ $n->title }}</div>
                    <p class="text-sm text-muted">{{ $n->body }}</p>
                    <p class="text-xs text-muted mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @unless($n->read_at)
                    <form method="POST" action="{{ route('user.notifications.read', $n) }}">@csrf
                        <button class="text-xs text-accent font-semibold">Tandai dibaca</button>
                    </form>
                @endunless
            </div>
        </div>
    @empty
        <p class="text-muted">Belum ada notifikasi.</p>
    @endforelse

    <div class="mt-3">{{ $notifications->links() }}</div>
@endsection
