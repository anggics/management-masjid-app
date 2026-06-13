@extends('layouts.admin')
@section('title', 'Override Jadwal Sholat')

@section('content')
    <p class="text-sm text-muted mb-4">Jadwal sholat otomatis dari Aladhan API. Gunakan form ini untuk mengoreksi waktu pada tanggal tertentu.</p>

    <form method="POST" action="{{ route('admin.prayer.store') }}" class="card mb-6 grid md:grid-cols-4 gap-3">
        @csrf
        <div class="md:col-span-4"><label class="label">Tanggal</label><input type="date" name="date" class="input" required></div>
        @foreach(['fajr' => 'Subuh', 'dhuha' => 'Dhuha', 'dhuhr' => 'Dzuhur', 'asr' => 'Ashar', 'maghrib' => 'Maghrib', 'isha' => 'Isya'] as $f => $label)
            <div><label class="label">{{ $label }}</label><input type="time" name="{{ $f }}" class="input"></div>
        @endforeach
        <div class="md:col-span-2"><label class="label">Catatan</label><input name="note" class="input"></div>
        <div class="md:col-span-4"><button class="btn-primary">Simpan Override</button></div>
    </form>

    <div class="card overflow-x-auto">
        <table class="table-admin">
            <thead><tr><th class="py-2">Tanggal</th><th>Subuh</th><th>Dzuhur</th><th>Ashar</th><th>Maghrib</th><th>Isya</th><th></th></tr></thead>
            <tbody>
                @forelse($overrides as $o)
                    <tr class="border-b last:border-0">
                        <td class="py-2 font-semibold">{{ $o->date->translatedFormat('d M Y') }}</td>
                        <td>{{ $o->fajr ? substr($o->fajr,0,5) : '—' }}</td>
                        <td>{{ $o->dhuhr ? substr($o->dhuhr,0,5) : '—' }}</td>
                        <td>{{ $o->asr ? substr($o->asr,0,5) : '—' }}</td>
                        <td>{{ $o->maghrib ? substr($o->maghrib,0,5) : '—' }}</td>
                        <td>{{ $o->isha ? substr($o->isha,0,5) : '—' }}</td>
                        <td class="text-right"><form method="POST" action="{{ route('admin.prayer.destroy', $o) }}" onsubmit="return confirm('Hapus override?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-4 text-center text-muted">Belum ada override.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $overrides->links() }}</div>
@endsection
