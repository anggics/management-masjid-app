@extends('layouts.admin')
@section('title', 'Metode Pembayaran')

@php
    $typeLabels = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'rekening_qurban' => 'Rekening Qurban'];
@endphp

@section('content')
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Form tambah: field tampil sesuai tipe --}}
        <form method="POST" action="{{ route('admin.payments.store') }}" enctype="multipart/form-data" class="card space-y-3 h-fit" x-data="{ type: '{{ old('type', 'qris') }}' }">
            @csrf
            <h2 class="font-bold">Tambah Metode</h2>
            <div>
                <label class="label">Tipe</label>
                <select name="type" class="input" x-model="type">
                    <option value="qris">QRIS</option>
                    <option value="bank_transfer">Transfer Bank</option>
                    <option value="rekening_qurban">Rekening Qurban</option>
                </select>
            </div>
            <div><label class="label">Label</label><input name="label" class="input" value="{{ old('label') }}" placeholder="mis. QRIS Masjid / BCA" required></div>

            {{-- QRIS: hanya upload gambar --}}
            <div x-show="type === 'qris'" x-cloak>
                <label class="label">Gambar QRIS</label>
                <input type="file" name="qris_image" accept="image/png,image/jpeg" class="input">
            </div>

            {{-- Transfer bank & rekening qurban: bank / no. rekening / atas nama --}}
            <template x-if="type === 'bank_transfer' || type === 'rekening_qurban'">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="label">Bank</label><input name="bank_name" class="input" value="{{ old('bank_name') }}"></div>
                        <div><label class="label">No. Rekening</label><input name="account_number" class="input" value="{{ old('account_number') }}"></div>
                    </div>
                    <div><label class="label">Atas Nama</label><input name="account_name" class="input" value="{{ old('account_name') }}"></div>
                </div>
            </template>

            {{-- Urutan hanya untuk qris & transfer bank --}}
            <div x-show="type === 'qris' || type === 'bank_transfer'" x-cloak>
                <label class="label">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="input">
            </div>

            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            <button class="btn-primary w-full">Tambah</button>
        </form>

        <div class="space-y-3">
            @forelse($methods as $m)
                <div class="card" x-data="{ edit: false, type: '{{ $m->type }}' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold">{{ $m->label }}</div>
                            <div class="text-sm text-muted">{{ $typeLabels[$m->type] ?? $m->type }}</div>
                            @if($m->type === 'bank_transfer' || $m->type === 'rekening_qurban')
                                <div class="text-sm font-mono">{{ $m->bank_name }} • {{ $m->account_number }} ({{ $m->account_name }})</div>
                            @endif
                        </div>
                        {{-- Toggle status aktif/nonaktif --}}
                        <form method="POST" action="{{ route('admin.payments.update', $m) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="type" value="{{ $m->type }}">
                            <input type="hidden" name="label" value="{{ $m->label }}">
                            <input type="hidden" name="bank_name" value="{{ $m->bank_name }}">
                            <input type="hidden" name="account_number" value="{{ $m->account_number }}">
                            <input type="hidden" name="account_name" value="{{ $m->account_name }}">
                            <input type="hidden" name="sort_order" value="{{ $m->sort_order }}">
                            <input type="hidden" name="is_active" value="{{ $m->is_active ? 0 : 1 }}">
                            <button type="submit" role="switch" aria-checked="{{ $m->is_active ? 'true' : 'false' }}"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $m->is_active ? 'bg-primary' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $m->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                    </div>
                    @if($m->qris_image_url)<img src="{{ $m->qris_image_url }}" class="w-28 mt-2 rounded-lg border" alt="qris">@endif
                    <div class="mt-2 flex gap-3 text-sm font-semibold">
                        <button class="text-primary" @click="edit = true">Edit</button>
                        <form method="POST" action="{{ route('admin.payments.destroy', $m) }}" class="inline" onsubmit="return confirm('Hapus metode ini?')">
                            @csrf @method('DELETE')<button class="text-red-600">Hapus</button>
                        </form>
                    </div>

                    <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                        <form method="POST" action="{{ route('admin.payments.update', $m) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl p-5 w-full max-w-md space-y-3 text-left max-h-[90vh] overflow-y-auto">
                            @csrf @method('PUT')
                            <h3 class="font-bold">Edit Metode Pembayaran</h3>
                            <div>
                                <label class="label">Tipe</label>
                                <select name="type" class="input" x-model="type">
                                    <option value="qris">QRIS</option>
                                    <option value="bank_transfer">Transfer Bank</option>
                                    <option value="rekening_qurban">Rekening Qurban</option>
                                </select>
                            </div>
                            <div><label class="label">Label</label><input name="label" class="input" value="{{ $m->label }}" required></div>

                            <div x-show="type === 'qris'" x-cloak>
                                <label class="label">Gambar QRIS (kosongkan jika tidak diganti)</label>
                                <input type="file" name="qris_image" accept="image/png,image/jpeg" class="input">
                            </div>

                            <template x-if="type === 'bank_transfer' || type === 'rekening_qurban'">
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div><label class="label">Bank</label><input name="bank_name" class="input" value="{{ $m->bank_name }}"></div>
                                        <div><label class="label">No. Rekening</label><input name="account_number" class="input" value="{{ $m->account_number }}"></div>
                                    </div>
                                    <div><label class="label">Atas Nama</label><input name="account_name" class="input" value="{{ $m->account_name }}"></div>
                                </div>
                            </template>

                            <div x-show="type === 'qris' || type === 'bank_transfer'" x-cloak>
                                <label class="label">Urutan</label>
                                <input type="number" name="sort_order" class="input" value="{{ $m->sort_order }}">
                            </div>

                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($m->is_active)> Aktif</label>
                            <div class="flex gap-2 justify-end">
                                <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                                <button class="btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <span class="empty-state-icon">💳</span>
                    <p class="font-semibold text-ink">Belum ada metode pembayaran</p>
                    <p class="text-sm">Tambahkan QRIS, transfer bank, atau rekening qurban.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
