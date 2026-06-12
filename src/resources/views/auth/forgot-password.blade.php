@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
    <div class="max-w-sm mx-auto">
        <h1 class="text-2xl font-bold mb-1">Lupa Password</h1>
        <p class="text-muted text-sm mb-5">Masukkan email akun Anda. Kami akan mengirim tautan untuk mengatur ulang password.</p>

        <form method="POST" action="{{ route('password.email') }}" class="card space-y-4">
            @csrf
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus>
            </div>
            <button class="btn-primary w-full">Kirim Tautan Reset</button>
            <p class="text-sm text-center text-muted">Ingat password? <a href="{{ route('login') }}" class="text-primary font-semibold">Masuk</a></p>
        </form>
    </div>
@endsection
