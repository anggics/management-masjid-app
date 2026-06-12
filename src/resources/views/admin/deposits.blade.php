@extends('layouts.admin')
@section('title', 'Verifikasi Setoran')

@section('content')
    @forelse($pending as $d)
        <div class="card mb-4" x-data="{ reject: false }">
            <div class="flex gap-4">
                <a href="{{ $d->proof_image_url }}" target="_blank">
                    <img src="{{ $d->proof_image_url }}" class="w-24 h-24 object-cover rounded-xl border" alt="bukti">
                </a>
                <div class="flex-1">
                    <div class="font-semibold">{{ $d->user?->name ?? '—' }}</div>
                    <div class="text-sm text-muted">{{ $d->participant?->name }} ({{ $d->participant?->animal_type }})</div>
                    <div class="text-lg font-bold text-primary">Rp {{ number_format($d->amount,0,',','.') }}</div>
                    @if($d->notes)<p class="text-sm text-muted">Catatan: {{ $d->notes }}</p>@endif
                    <div class="text-xs text-muted">{{ $d->created_at->diffForHumans() }}</div>

                    <div class="flex gap-2 mt-3">
                        <form method="POST" action="{{ route('admin.deposits.verify', $d) }}">@csrf
                            <button class="btn-primary px-4 py-2 text-sm">✅ Verifikasi</button>
                        </form>
                        <button @click="reject = !reject" class="btn-danger px-4 py-2 text-sm">✕ Tolak</button>
                    </div>

                    <form x-show="reject" x-cloak method="POST" action="{{ route('admin.deposits.reject', $d) }}" class="mt-3 flex gap-2">@csrf
                        <input name="rejection_reason" class="input" placeholder="Alasan penolakan" required>
                        <button class="btn-danger px-4">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center text-muted">Tidak ada setoran menunggu verifikasi. 🎉</div>
    @endforelse

    <div class="mt-3">{{ $pending->links() }}</div>
@endsection
