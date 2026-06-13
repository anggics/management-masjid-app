@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
    <div class="max-w-sm mx-auto">
        <div class="flex flex-col items-center text-center mb-5">
            @if(($mosque ?? null) && $mosque->logo_url)
                <img src="{{ $mosque->logo_url }}" alt="Logo" class="w-16 h-16 rounded-full object-cover shadow-sm mb-3">
            @else
                <span class="w-16 h-16 rounded-full bg-primary text-gold flex items-center justify-center text-3xl shadow-sm mb-3">☪</span>
            @endif
            <h1 class="text-2xl font-bold">Masuk</h1>
            <p class="text-muted text-sm">Masuk untuk mengikuti tabungan qurban.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card space-y-4">
            @csrf
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus>
            </div>
            <div>
                <label class="label">Password</label>
                <input type="password" name="password" class="input" required>
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="remember"> Ingat saya</label>
                <a href="{{ route('password.request') }}" class="text-primary font-semibold">Lupa password?</a>
            </div>
            <button class="btn-primary w-full">Masuk</button>
            <p class="text-sm text-center text-muted">Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-semibold">Daftar</a></p>
        </form>

        <!-- <div class="card mt-4 text-xs text-muted">
            <p class="font-semibold mb-1">Akun demo:</p>
            <p>admin@masjid.test / staff@masjid.test / user@masjid.test</p>
            <p>Password: <span class="font-mono">password</span></p>
        </div> -->
    </div>
@endsection
