@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
    <div class="max-w-sm mx-auto">
        <div class="flex flex-col items-center text-center mb-5">
            @if(($mosque ?? null) && $mosque->logo_url)
                <img src="{{ $mosque->logo_url }}" alt="Logo" class="w-16 h-16 rounded-full object-cover shadow-sm mb-3">
            @else
                <span class="w-16 h-16 rounded-full bg-primary text-gold flex items-center justify-center text-3xl shadow-sm mb-3">☪</span>
            @endif
            <h1 class="text-2xl font-bold">Daftar Akun</h1>
            <p class="text-muted text-sm">Buat akun untuk menabung qurban.</p>
        </div>

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
