@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
    <div class="max-w-sm mx-auto">
        <h1 class="text-2xl font-bold mb-1">Daftar Akun</h1>
        <p class="text-muted text-sm mb-5">Buat akun untuk menabung qurban.</p>

        <form method="POST" action="{{ route('register') }}" class="card space-y-4">
            @csrf
            <div>
                <label class="label">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="input" required autofocus>
            </div>
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input" required>
                <p class="text-xs text-muted mt-1">Email harus valid dan aktif.</p>
            </div>
            <div>
                <label class="label">Nomor WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="input" placeholder="08xxxx" required>
            </div>
            <div>
                <label class="label">Password</label>
                <input type="password" name="password" class="input" required>
            </div>
            <div>
                <label class="label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="input" required>
            </div>
            <button class="btn-primary w-full">Daftar</button>
            <p class="text-sm text-center text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-semibold">Masuk</a></p>
        </form>
    </div>
@endsection
