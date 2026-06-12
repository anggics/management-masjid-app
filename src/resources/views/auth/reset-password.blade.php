@extends('layouts.app')
@section('title', 'Reset Password')

@section('content')
    <div class="max-w-sm mx-auto">
        <h1 class="text-2xl font-bold mb-1">Atur Password Baru</h1>
        <p class="text-muted text-sm mb-5">Buat password baru untuk akun Anda.</p>

        <form method="POST" action="{{ route('password.update') }}" class="card space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" class="input" required readonly>
            </div>
            <div>
                <label class="label">Password Baru</label>
                <input type="password" name="password" class="input" required autofocus>
            </div>
            <div>
                <label class="label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="input" required>
            </div>
            <button class="btn-primary w-full">Simpan Password</button>
        </form>
    </div>
@endsection
