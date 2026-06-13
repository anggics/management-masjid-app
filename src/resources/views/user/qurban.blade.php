@extends('layouts.app')
@section('title', 'Tabungan Qurban ' . $user->name)

@section('content')
    <h1 class="page-title mb-3">Tabungan Qurban {{ $user->name }}</h1>

    {{-- Nomor rekening qurban (dari metode pembayaran tipe rekening qurban) --}}
    @if($qurbanAccounts->isNotEmpty())
        <div class="card mb-4">
            <p class="font-semibold mb-2">Rekening Qurban</p>
            <div class="space-y-2">
                @foreach($qurbanAccounts as $acc)
                    <div class="text-sm border-b last:border-0 pb-2 last:pb-0">
                        <div class="font-semibold">
                            {{ $acc->bank_name }} —
                            <button onclick="copyToClipboard('{{ $acc->account_number }}', this)" class="font-mono font-bold text-primary">{{ $acc->account_number }} 📋</button>
                        </div>
                        <div class="text-muted">a.n. {{ $acc->account_name }} <span class="text-xs">({{ $acc->label }})</span></div>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-muted mt-2">Transfer ke salah satu rekening di atas, lalu kirim bukti setoran pada tabungan Anda.</p>
        </div>
    @endif

    {{-- Daftar peserta baru --}}
    <details class="card mb-4">
        <summary class="font-semibold cursor-pointer">+ Daftar peserta qurban baru</summary>
        <form method="POST" action="{{ route('user.qurban.register') }}" class="mt-3 space-y-3">
            @csrf
            <div>
                <label class="label">Nama / Label</label>
                <input type="text" name="name" class="input" placeholder="mis. Qurban Keluarga Budi" required>
            </div>
            <div>
                <label class="label">Jenis</label>
                <select name="qurban_type_id" class="input" required>
                    @forelse($types as $t)
                        <option value="{{ $t->id }}">{{ $t->label() }}</option>
                    @empty
                        <option value="" disabled>Belum ada pilihan jenis qurban</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label class="label">Tahun Qurban</label>
                <select name="qurban_year_id" class="input" required>
                    @forelse($years as $y)
                        <option value="{{ $y->id }}">{{ $y->label() }}</option>
                    @empty
                        <option value="" disabled>Belum ada pilihan tahun qurban</option>
                    @endforelse
                </select>
            </div>
            <button class="btn-primary w-full" {{ $types->isEmpty() || $years->isEmpty() ? 'disabled' : '' }}>Daftar</button>
        </form>
        @if($types->isEmpty())<p class="text-muted text-sm mt-2">Admin belum menambahkan jenis qurban. Silakan coba lagi nanti.</p>@endif
    </details>

    @forelse($participants as $p)
        <div class="card mb-4" x-data="{ editLabel: false }">
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-semibold flex items-center gap-2">
                        <span x-show="!editLabel">{{ $p->name }}</span>
                        <button type="button" x-show="!editLabel" @click="editLabel = true" class="text-xs text-primary font-normal underline">edit label</button>
                    </div>
                    <div class="text-sm text-muted capitalize">{{ $p->qurbanType?->label() ?? $p->animal_type }}</div>
                </div>
                <span class="badge-{{ $p->status === 'completed' ? 'verified' : 'pending' }} capitalize">{{ $p->status }}</span>
            </div>

            {{-- Edit label (hanya nama label, hanya pemilik) --}}
            <form x-show="editLabel" x-cloak method="POST" action="{{ route('user.qurban.label', $p) }}" class="mt-2 flex gap-2">
                @csrf @method('PUT')
                <input name="name" class="input flex-1" value="{{ $p->name }}" required>
                <button class="btn-primary">Simpan</button>
                <button type="button" class="btn-secondary" @click="editLabel = false">Batal</button>
            </form>

            <div class="mt-3">
                <div class="h-2 bg-surface-2 rounded-full overflow-hidden">
                    <div class="h-full bg-accent" style="width: {{ $p->progressPercent() }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-muted mt-1">
                    <span>Rp {{ number_format($p->collected_amount,0,',','.') }}</span>
                    <span>dari Rp {{ number_format($p->target_amount,0,',','.') }}</span>
                </div>
            </div>

            {{-- Upload bukti transfer --}}
            <details class="mt-3">
                <summary class="text-sm font-semibold text-primary cursor-pointer">+ Kirim bukti setoran</summary>
                <form method="POST" action="{{ route('user.qurban.deposit', $p) }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="label">Nominal (Rp)</label>
                        <input type="number" name="amount" class="input" min="1" required>
                    </div>
                    <div>
                        <label class="label">Bukti Transfer (JPG/PNG, maks 5MB)</label>
                        <input type="file" name="proof" accept="image/png,image/jpeg" class="input" required>
                    </div>
                    <div>
                        <label class="label">Catatan (opsional)</label>
                        <input type="text" name="notes" class="input">
                    </div>
                    <button class="btn-primary w-full">Kirim</button>
                </form>
            </details>

            {{-- Riwayat setoran --}}
            @if($p->deposits->isNotEmpty())
                <div class="mt-3 border-t pt-3">
                    <p class="text-sm font-semibold mb-2">Riwayat Setoran</p>
                    @foreach($p->deposits as $d)
                        <div class="flex justify-between items-center text-sm py-1">
                            <span>{{ $d->created_at->translatedFormat('d M Y') }} • Rp {{ number_format($d->amount,0,',','.') }}</span>
                            <span class="badge-{{ $d->status === 'verified' ? 'verified' : ($d->status === 'rejected' ? 'rejected' : 'pending') }}">{{ $d->status }}</span>
                        </div>
                        @if($d->status === 'rejected' && $d->rejection_reason)
                            <p class="text-xs text-red-600">Alasan: {{ $d->rejection_reason }}</p>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <span class="empty-state-icon">🐐</span>
            <p class="font-semibold text-ink">Belum ada tabungan qurban</p>
            <p class="text-sm">Daftar peserta di atas untuk memulai tabungan Anda.</p>
        </div>
    @endforelse
@endsection
