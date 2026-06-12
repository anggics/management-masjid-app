@extends('layouts.app')
@section('title', 'Sedekah Jumat')

@section('content')
    <h1 class="text-xl font-bold mb-1">Sedekah Jumat Digital</h1>
    <p class="text-muted text-sm mb-4">Tunaikan sedekah Anda melalui QRIS atau transfer bank. Tanpa perlu login.</p>

    @forelse($methods as $m)
        <div class="card mb-4">
            @if($m->type === 'qris')
                <div class="text-center">
                    <h2 class="font-bold mb-2">{{ $m->label }}</h2>
                    @if($m->qris_image_url)
                        <img src="{{ $m->qris_image_url }}" alt="QRIS" class="mx-auto max-w-xs w-full rounded-xl border">
                    @else
                        <div class="bg-surface-2 rounded-xl py-10 text-muted">QRIS belum diunggah</div>
                    @endif
                    <p class="text-sm text-muted mt-2">Pindai kode QR di atas dengan aplikasi pembayaran Anda.</p>
                </div>
            @else
                <h2 class="font-bold mb-2">{{ $m->label }}</h2>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-muted">Bank</span><span class="font-semibold">{{ $m->bank_name }}</span></div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted">No. Rekening</span>
                        <button onclick="copyToClipboard('{{ $m->account_number }}', this)" class="font-mono font-bold text-primary">{{ $m->account_number }} 📋</button>
                    </div>
                    <div class="flex justify-between"><span class="text-muted">Atas Nama</span><span class="font-semibold">{{ $m->account_name }}</span></div>
                </div>
            @endif
        </div>
    @empty
        <p class="text-muted">Metode pembayaran belum tersedia.</p>
    @endforelse
@endsection
