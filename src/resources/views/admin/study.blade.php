@extends('layouts.admin')
@section('title', 'Jadwal Kajian')

@section('content')
    <details class="card mb-4" {{ $errors->any() ? 'open' : '' }}>
        <summary class="font-semibold cursor-pointer">+ Tambah kajian</summary>
        <form method="POST" action="{{ route('admin.study.store') }}" enctype="multipart/form-data" class="mt-3 grid md:grid-cols-2 gap-3">
            @csrf
            <div><label class="label">Judul</label><input name="title" class="input" value="{{ old('title') }}" required></div>
            <div><label class="label">Pemateri</label><input name="speaker" class="input" value="{{ old('speaker') }}" required></div>
            <div><label class="label">Waktu</label><input type="datetime-local" name="scheduled_at" class="input" required></div>
            <div><label class="label">Lokasi</label><input name="location" class="input" value="{{ old('location') }}" required></div>
            <div><label class="label">Status</label><select name="status" class="input"><option value="upcoming">Mendatang</option><option value="ongoing">Berlangsung</option><option value="done">Selesai</option><option value="cancelled">Dibatalkan</option></select></div>
            <div><label class="label">Poster (opsional)</label><input type="file" name="poster" accept="image/png,image/jpeg" class="input"></div>
            <div class="md:col-span-2"><label class="label">Deskripsi</label><textarea name="description" class="input" rows="2"></textarea></div>
            <div class="md:col-span-2"><button class="btn-primary">Simpan</button></div>
        </form>
    </details>

    {{-- Filter --}}
    <form method="GET" class="card mb-4 grid md:grid-cols-3 gap-3 items-end">
        <div><label class="label">Cari Nama Kajian</label><input name="search" class="input" value="{{ $filters['search'] }}" placeholder="Judul kajian"></div>
        <div><label class="label">Tanggal</label><input type="date" name="date" class="input" value="{{ $filters['date'] }}"></div>
        <div class="flex gap-2">
            <button class="btn-primary">Filter</button>
            <a href="{{ route('admin.study.index') }}" class="btn-secondary">Reset</a>
        </div>
    </form>

    @forelse($schedules as $k)
        <div class="card mb-3 flex justify-between items-start gap-3" x-data="{ edit: false }">
            <div class="flex gap-3">
                @if($k->poster_url)
                    <img src="{{ $k->poster_url }}" class="w-20 h-20 object-cover rounded-lg border" alt="poster {{ $k->title }}">
                @endif
                <div>
                    <div class="font-semibold">{{ $k->title }}</div>
                    <div class="text-sm text-muted">{{ $k->speaker }} • {{ $k->scheduled_at->translatedFormat('d M Y, H:i') }} • {{ $k->location }}</div>
                    <span class="badge-{{ $k->status === 'cancelled' ? 'rejected' : ($k->status === 'done' ? 'verified' : 'pending') }} capitalize">{{ $k->status }}</span>
                </div>
            </div>
            <div class="text-right whitespace-nowrap">
                <button class="text-primary mr-2" @click="edit = true">Edit</button>
                <form method="POST" action="{{ route('admin.study.destroy', $k) }}" class="inline" onsubmit="return confirm('Hapus kajian ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>

                <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                    <form method="POST" action="{{ route('admin.study.update', $k) }}" enctype="multipart/form-data" class="bg-white rounded-xl p-5 w-full max-w-lg space-y-3 text-left max-h-[90vh] overflow-y-auto">
                        @csrf @method('PUT')
                        <h3 class="font-bold">Edit Kajian</h3>
                        <div><label class="label">Judul</label><input name="title" class="input" value="{{ $k->title }}" required></div>
                        <div><label class="label">Pemateri</label><input name="speaker" class="input" value="{{ $k->speaker }}" required></div>
                        <div><label class="label">Waktu</label><input type="datetime-local" name="scheduled_at" class="input" value="{{ $k->scheduled_at->format('Y-m-d\TH:i') }}" required></div>
                        <div><label class="label">Lokasi</label><input name="location" class="input" value="{{ $k->location }}" required></div>
                        <div>
                            <label class="label">Status</label>
                            <select name="status" class="input">
                                <option value="upcoming" @selected($k->status === 'upcoming')>Mendatang</option>
                                <option value="ongoing" @selected($k->status === 'ongoing')>Berlangsung</option>
                                <option value="done" @selected($k->status === 'done')>Selesai</option>
                                <option value="cancelled" @selected($k->status === 'cancelled')>Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Poster (kosongkan jika tidak diganti)</label>
                            <input type="file" name="poster" accept="image/png,image/jpeg" class="input">
                            @if($k->poster_url)<img src="{{ $k->poster_url }}" class="w-24 mt-2 rounded border" alt="poster">@endif
                        </div>
                        <div><label class="label">Deskripsi</label><textarea name="description" class="input" rows="2">{{ $k->description }}</textarea></div>
                        <div class="flex gap-2 justify-end">
                            <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                            <button class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Belum ada kajian.</p>
    @endforelse
    <div class="mt-3">{{ $schedules->links() }}</div>
@endsection
