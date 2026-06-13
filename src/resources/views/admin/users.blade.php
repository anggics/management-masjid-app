@extends('layouts.admin')
@section('title', 'Pengguna')

@section('content')
    <details class="card mb-4" {{ $errors->any() ? 'open' : '' }}>
        <summary class="font-semibold cursor-pointer">+ Tambah pengguna</summary>
        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-3 grid md:grid-cols-2 gap-3">
            @csrf
            <div><label class="label">Nama</label><input name="name" class="input" value="{{ old('name') }}" required></div>
            <div><label class="label">Email</label><input type="email" name="email" class="input" value="{{ old('email') }}" required></div>
            <div><label class="label">No. WhatsApp</label><input name="whatsapp" class="input" value="{{ old('whatsapp') }}" placeholder="08xxxx"></div>
            <div>
                <label class="label">Role</label>
                <select name="role" class="input">
                    <option value="user">User</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div><label class="label">Password</label><input type="password" name="password" class="input" required></div>
            <div><label class="label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="input" required></div>
            <div class="md:col-span-2"><button class="btn-primary">Simpan</button></div>
        </form>
        @if($errors->any())<ul class="text-red-600 text-sm mt-2 list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
    </details>

    <form method="GET" class="card mb-4 flex flex-wrap gap-2 items-end">
        <div class="flex-1 min-w-[200px]"><label class="label">Cari (nama / email)</label><input name="search" class="input" value="{{ $search }}"></div>
        <button class="btn-primary">Cari</button>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
    </form>

    <div class="card overflow-x-auto">
        <table class="table-admin">
            <thead><tr><th class="py-2">Nama</th><th>Email</th><th>WhatsApp</th><th>Role</th><th></th></tr></thead>
            <tbody>
                @forelse($users as $u)
                    <tr class="border-b last:border-0" x-data="{ edit: false }">
                        <td class="py-2 font-semibold">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->whatsapp ?: '—' }}</td>
                        <td><span class="badge-{{ $u->role === 'admin' ? 'verified' : ($u->role === 'staff' ? 'pending' : 'rejected') }} capitalize">{{ $u->role }}</span></td>
                        <td class="text-right whitespace-nowrap">
                            <button class="text-primary mr-2" @click="edit = true">Edit</button>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                            @endif

                            <div x-show="edit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="edit = false">
                                <form method="POST" action="{{ route('admin.users.update', $u) }}" class="bg-white rounded-2xl shadow-xl p-5 w-full max-w-md space-y-3 text-left">
                                    @csrf @method('PUT')
                                    <h3 class="font-bold">Edit Pengguna</h3>
                                    <div><label class="label">Nama</label><input name="name" class="input" value="{{ $u->name }}" required></div>
                                    <div><label class="label">Email</label><input type="email" name="email" class="input" value="{{ $u->email }}" required></div>
                                    <div><label class="label">No. WhatsApp</label><input name="whatsapp" class="input" value="{{ $u->whatsapp }}"></div>
                                    <div>
                                        <label class="label">Role</label>
                                        <select name="role" class="input">
                                            <option value="user" @selected($u->role === 'user')>User</option>
                                            <option value="staff" @selected($u->role === 'staff')>Staff</option>
                                            <option value="admin" @selected($u->role === 'admin')>Admin</option>
                                        </select>
                                    </div>
                                    <div><label class="label">Password baru (opsional)</label><input type="password" name="password" class="input"></div>
                                    <div><label class="label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="input"></div>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" class="btn-secondary" @click="edit = false">Batal</button>
                                        <button class="btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-muted">Belum ada pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
@endsection
