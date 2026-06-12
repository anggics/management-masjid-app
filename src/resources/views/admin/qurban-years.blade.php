@extends('layouts.admin')
@section('title', 'Tahun Qurban')

@section('content')
    <details class="card mb-4" {{ $errors->any() ? 'open' : '' }}>
        <summary class="font-semibold cursor-pointer">+ Tambah tahun qurban (Hijriah)</summary>
        <form method="POST" action="{{ route('admin.qurban-years.store') }}" class="mt-3 grid md:grid-cols-3 gap-3 items-end">
            @csrf
            <div>
                <label class="label">Tahun Hijriah</label>
                <input type="number" name="hijri_year" class="input" min="1300" max="1600" value="{{ old('hijri_year', 1447) }}" required>
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm mb-2"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
                <button class="btn-primary w-full">Simpan</button>
            </div>
        </form>
        @error('hijri_year')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
    </details>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-muted border-b"><th class="py-2">Tahun Hijriah</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($years as $y)
                    <tr class="border-b last:border-0">
                        <td class="py-2 font-semibold">{{ $y->label() }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.qurban-years.update', $y) }}" class="inline">
                                @csrf @method('PUT')
                                <button class="badge-{{ $y->is_active ? 'verified' : 'rejected' }}">{{ $y->is_active ? 'Aktif' : 'Nonaktif' }}</button>
                            </form>
                        </td>
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.qurban-years.destroy', $y) }}" class="inline" onsubmit="return confirm('Hapus tahun ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-muted">Belum ada tahun qurban. Tambahkan agar bisa dipilih saat input data qurban.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-xs text-muted mt-2">Klik badge status untuk mengaktifkan/menonaktifkan tahun.</p>
@endsection
