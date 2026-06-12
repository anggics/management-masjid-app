@extends('layouts.admin')
@section('title', 'Profil Masjid')

@section('content')
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="card max-w-2xl space-y-4">
        @csrf @method('PUT')

        <div class="flex items-center gap-4">
            @if($mosque->logo_url)
                <img src="{{ $mosque->logo_url }}" class="w-16 h-16 rounded-full object-cover border" alt="logo">
            @else
                <div class="w-16 h-16 rounded-full bg-surface-2 flex items-center justify-center text-2xl">🕌</div>
            @endif
            <div class="flex-1">
                <label class="label">Logo (PNG/JPG, maks 2MB)</label>
                <input type="file" name="logo" accept="image/png,image/jpeg" class="input">
            </div>
        </div>

        <div>
            <label class="label">Nama Masjid</label>
            <input type="text" name="name" value="{{ old('name', $mosque->name) }}" class="input" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Kota</label>
                <input type="text" name="city" value="{{ old('city', $mosque->city) }}" class="input" required>
            </div>
            <div>
                <label class="label">Metode Kalkulasi Jadwal Sholat</label>
                <select name="prayer_method" class="input" required>
                    @foreach($prayerMethods as $method)
                        <option value="{{ $method }}" @selected(old('prayer_method', $mosque->prayer_method) === $method)>
                            {{ $method === 'Kemenag' ? 'Kemenag RI' : $method }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="label">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $mosque->phone) }}" class="input">
        </div>
        <div>
            <label class="label">Alamat</label>
            <textarea name="address" class="input" rows="2">{{ old('address', $mosque->address) }}</textarea>
        </div>
        <div>
            <label class="label">Deskripsi</label>
            <textarea name="description" class="input" rows="3">{{ old('description', $mosque->description) }}</textarea>
        </div>

        <button class="btn-primary">Simpan Perubahan</button>
    </form>
@endsection
