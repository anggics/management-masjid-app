@extends('layouts.admin')
@section('title', 'Panitia Qurban')

@section('content')
    <details class="card mb-4" {{ $errors->any() ? 'open' : '' }}>
        <summary class="font-semibold cursor-pointer">+ Tambah panitia</summary>
        <form method="POST" action="{{ route('admin.committees.store') }}" class="mt-3 grid md:grid-cols-3 gap-3">
            @csrf
            <div><label class="label">Nama</label><input name="name" class="input" value="{{ old('name') }}" required></div>
            <div><label class="label">Alamat</label><input name="address" class="input" value="{{ old('address') }}"></div>
            <div><label class="label">No. WhatsApp</label><input name="whatsapp" class="input" value="{{ old('whatsapp') }}" placeholder="08xxxx"></div>
            <div class="md:col-span-3"><button class="btn-primary">Simpan</button></div>
        </form>
    </details>

    {{-- Search berdasarkan nama atau alamat --}}
    <form method="GET" class="card mb-4 flex flex-wrap gap-2 items-end">
        <div class="flex-1 min-w-[200px]"><label class="label">Cari (nama / alamat)</label><input name="search" class="input" value="{{ $search }}"></div>
        <button class="btn-primary">Cari</button>
        <a href="{{ route('admin.committees.index') }}" class="btn-secondary">Reset</a>
    </form>

    <div class="card overflow-x-auto">
        <table class="table-admin">
            <thead><tr><th class="py-2">Nama</th><th>Alamat</th><th>WhatsApp</th><th></th></tr></thead>
            <tbody>
                @forelse($committees as $c)
                    <tr class="border-b last:border-0" x-data="{ edit: false }">
                        <td class="py-2 font-semibold">{{ $c->name }}</td>
                        <td>{{ $c->address ?: '—' }}</td>
                        <td>{{ $c->whatsapp ?: '—' }}</td>
                        <td class="text-right whitespace-nowrap">
                            <button class="text-primary mr-2" @click="edit = true">Edit</button>
                            <form method="POST" action="{{ route('admin.committees.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus panitia ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>

                            <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                                <form method="POST" action="{{ route('admin.committees.update', $c) }}" class="bg-white rounded-2xl shadow-xl p-5 w-full max-w-md space-y-3 text-left">
                                    @csrf @method('PUT')
                                    <h3 class="font-bold">Edit Panitia</h3>
                                    <div><label class="label">Nama</label><input name="name" class="input" value="{{ $c->name }}" required></div>
                                    <div><label class="label">Alamat</label><input name="address" class="input" value="{{ $c->address }}"></div>
                                    <div><label class="label">No. WhatsApp</label><input name="whatsapp" class="input" value="{{ $c->whatsapp }}"></div>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                                        <button class="btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-muted">Belum ada panitia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $committees->links() }}</div>
@endsection
