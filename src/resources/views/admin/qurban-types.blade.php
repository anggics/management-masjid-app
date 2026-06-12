@extends('layouts.admin')
@section('title', 'Jenis Hewan Qurban')

@section('content')
    <details class="card mb-4" {{ $errors->any() ? 'open' : '' }}>
        <summary class="font-semibold cursor-pointer">+ Tambah jenis hewan qurban</summary>
        <form method="POST" action="{{ route('admin.qurban-types.store') }}" class="mt-3 grid md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <label class="label">Jenis Hewan</label>
                <select name="animal_type" class="input" required>
                    <option value="kambing">Kambing</option>
                    <option value="sapi">Sapi</option>
                </select>
            </div>
            <div>
                <label class="label">Jenis Qurban</label>
                <select name="share_type" class="input" required>
                    <option value="individu">Individu</option>
                    <option value="group">Group</option>
                </select>
            </div>
            <div>
                <label class="label">Nominal Target (Rp)</label>
                <input type="number" name="target_amount" class="input" min="0" value="{{ old('target_amount', 2000000) }}" required>
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm mb-2"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
                <button class="btn-primary w-full">Simpan</button>
            </div>
        </form>
        @error('target_amount')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
    </details>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-muted border-b"><th class="py-2">Jenis Hewan</th><th>Jenis Qurban</th><th>Target</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($types as $t)
                    <tr class="border-b last:border-0" x-data="{ edit: false }">
                        <td class="py-2 font-semibold capitalize">{{ $t->animal_type }}</td>
                        <td class="capitalize">{{ $t->share_type }}</td>
                        <td>Rp {{ number_format($t->target_amount,0,',','.') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.qurban-types.update', $t) }}" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="animal_type" value="{{ $t->animal_type }}">
                                <input type="hidden" name="share_type" value="{{ $t->share_type }}">
                                <input type="hidden" name="target_amount" value="{{ $t->target_amount }}">
                                <input type="hidden" name="is_active" value="{{ $t->is_active ? 0 : 1 }}">
                                <button class="badge-{{ $t->is_active ? 'verified' : 'rejected' }}">{{ $t->is_active ? 'Aktif' : 'Nonaktif' }}</button>
                            </form>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <button class="text-primary mr-2" @click="edit = true">Edit</button>
                            <form method="POST" action="{{ route('admin.qurban-types.destroy', $t) }}" class="inline" onsubmit="return confirm('Hapus jenis ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>

                            <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                                <form method="POST" action="{{ route('admin.qurban-types.update', $t) }}" class="bg-white rounded-xl p-5 w-full max-w-md space-y-3 text-left">
                                    @csrf @method('PUT')
                                    <h3 class="font-bold">Edit Jenis Hewan Qurban</h3>
                                    <div>
                                        <label class="label">Jenis Hewan</label>
                                        <select name="animal_type" class="input" required>
                                            <option value="kambing" @selected($t->animal_type === 'kambing')>Kambing</option>
                                            <option value="sapi" @selected($t->animal_type === 'sapi')>Sapi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Jenis Qurban</label>
                                        <select name="share_type" class="input" required>
                                            <option value="individu" @selected($t->share_type === 'individu')>Individu</option>
                                            <option value="group" @selected($t->share_type === 'group')>Group</option>
                                        </select>
                                    </div>
                                    <div><label class="label">Nominal Target (Rp)</label><input type="number" name="target_amount" class="input" min="0" value="{{ (int) $t->target_amount }}" required></div>
                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($t->is_active)> Aktif</label>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                                        <button class="btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-muted">Belum ada jenis hewan qurban. Tambahkan dulu agar bisa dipilih saat mendaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-xs text-muted mt-2">Klik badge status untuk mengaktifkan/menonaktifkan. Jenis nonaktif tidak muncul di pilihan pendaftaran.</p>
@endsection
