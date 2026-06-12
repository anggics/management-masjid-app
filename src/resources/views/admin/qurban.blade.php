@extends('layouts.admin')
@section('title', 'Data Qurban')

@section('content')
    <details class="card mb-4" {{ $errors->any() ? 'open' : '' }}>
        <summary class="font-semibold cursor-pointer">+ Tambah peserta qurban</summary>
        <form method="POST" action="{{ route('admin.qurban.store') }}" class="mt-3 grid md:grid-cols-2 gap-3">
            @csrf
            <div><label class="label">Nama</label><input name="name" class="input" value="{{ old('name') }}" required></div>
            <div>
                <label class="label">Jenis</label>
                <select name="qurban_type_id" class="input" required>
                    @forelse($types as $t)
                        <option value="{{ $t->id }}">{{ $t->label() }}</option>
                    @empty
                        <option value="" disabled>Belum ada jenis — tambahkan di menu Jenis Hewan Qurban</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label class="label">Tahun Qurban</label>
                <select name="qurban_year_id" class="input" required>
                    @forelse($years as $y)
                        <option value="{{ $y->id }}">{{ $y->label() }}</option>
                    @empty
                        <option value="" disabled>Belum ada tahun — tambahkan di menu Tahun Qurban</option>
                    @endforelse
                </select>
            </div>
            <div><label class="label">Terkumpul (Rp)</label><input type="number" name="collected_amount" class="input" value="0"></div>
            <div><label class="label">Status</label><select name="status" class="input"><option value="active">Aktif</option><option value="completed">Selesai</option><option value="cancelled">Batal</option></select></div>
            <div class="md:col-span-2"><button class="btn-primary" {{ ($types->isEmpty() || $years->isEmpty()) ? 'disabled' : '' }}>Simpan</button></div>
        </form>
        @if($types->isEmpty())<p class="text-amber-600 text-sm mt-2">Tambahkan minimal satu <a href="{{ route('admin.qurban-types.index') }}" class="underline">jenis hewan qurban</a> terlebih dahulu.</p>@endif
        @if($years->isEmpty())<p class="text-amber-600 text-sm mt-2">Tambahkan minimal satu <a href="{{ route('admin.qurban-years.index') }}" class="underline">tahun qurban</a> terlebih dahulu.</p>@endif
    </details>

    {{-- Filter --}}
    <form method="GET" class="card mb-4 grid md:grid-cols-4 gap-3 items-end">
        <div><label class="label">Cari Nama</label><input name="search" class="input" value="{{ $filters['search'] }}" placeholder="Nama peserta"></div>
        <div>
            <label class="label">Jenis Hewan</label>
            <select name="animal_type" class="input">
                <option value="">Semua</option>
                <option value="kambing" @selected($filters['animal_type'] === 'kambing')>Kambing</option>
                <option value="sapi" @selected($filters['animal_type'] === 'sapi')>Sapi</option>
            </select>
        </div>
        <div>
            <label class="label">Tahun</label>
            <select name="qurban_year_id" class="input">
                <option value="">Semua</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}" @selected((string) $filters['qurban_year_id'] === (string) $y->id)>{{ $y->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button class="btn-primary">Filter</button>
            <a href="{{ route('admin.qurban.index') }}" class="btn-secondary">Reset</a>
        </div>
    </form>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-muted border-b"><th class="py-2">Nama</th><th>No. WhatsApp</th><th>Jenis</th><th>Tahun</th><th>Terkumpul</th><th>Target</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($participants as $p)
                    <tr class="border-b last:border-0" x-data="{ edit: false }">
                        <td class="py-2 font-semibold">{{ $p->name }}</td>
                        <td>{{ $p->user?->whatsapp ?: '—' }}</td>
                        <td class="capitalize">{{ $p->qurbanType?->label() ?? $p->animal_type }}</td>
                        <td>{{ $p->qurbanYear?->label() ?? '—' }}</td>
                        <td>Rp {{ number_format($p->collected_amount,0,',','.') }}</td>
                        <td>Rp {{ number_format($p->target_amount,0,',','.') }}</td>
                        <td><span class="badge-{{ $p->status === 'completed' ? 'verified' : ($p->status === 'cancelled' ? 'rejected' : 'pending') }} capitalize">{{ $p->status }}</span></td>
                        <td class="text-right whitespace-nowrap">
                            <button class="text-primary mr-2" @click="edit = true">Edit</button>
                            <form method="POST" action="{{ route('admin.qurban.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus data qurban ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>

                            {{-- Modal edit --}}
                            <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                                <form method="POST" action="{{ route('admin.qurban.update', $p) }}" class="bg-white rounded-xl p-5 w-full max-w-md space-y-3 text-left">
                                    @csrf @method('PUT')
                                    <h3 class="font-bold">Edit Data Qurban</h3>
                                    <div><label class="label">Nama</label><input name="name" class="input" value="{{ $p->name }}" required></div>
                                    <div>
                                        <label class="label">Jenis</label>
                                        <select name="qurban_type_id" class="input" required>
                                            @foreach($types as $t)
                                                <option value="{{ $t->id }}" @selected($p->qurban_type_id === $t->id)>{{ $t->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Tahun Qurban</label>
                                        <select name="qurban_year_id" class="input" required>
                                            @foreach($years as $y)
                                                <option value="{{ $y->id }}" @selected($p->qurban_year_id === $y->id)>{{ $y->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="label">Terkumpul (Rp)</label><input type="number" name="collected_amount" class="input" value="{{ (int) $p->collected_amount }}"></div>
                                    <div>
                                        <label class="label">Status</label>
                                        <select name="status" class="input">
                                            <option value="active" @selected($p->status === 'active')>Aktif</option>
                                            <option value="completed" @selected($p->status === 'completed')>Selesai</option>
                                            <option value="cancelled" @selected($p->status === 'cancelled')>Batal</option>
                                        </select>
                                    </div>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                                        <button class="btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-4 text-center text-muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $participants->links() }}</div>
@endsection
