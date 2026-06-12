@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
    <section class="card bg-gradient-to-br from-primary to-primary-light text-white border-0 mb-5"
             x-data="prayerCountdown(@js($prayer['times']))">
        <p class="text-sm text-white/80">Assalamu'alaikum, selamat datang di</p>
        <h1 class="text-2xl font-bold">{{ $mosque->name }}</h1>
        <p class="text-white/80 text-sm mt-1">{{ $mosque->address }}</p>

        <div class="mt-4 bg-white/10 rounded-xl p-4">
            <p class="text-sm text-white/80">Waktu sholat berikutnya</p>
            <div class="flex items-baseline gap-3">
                <span class="text-3xl font-bold" x-text="nextName">—</span>
                <span class="font-mono text-2xl" x-text="nextTime"></span>
            </div>
            <p class="text-white/70 text-sm mt-1" x-text="countdown"></p>
        </div>
    </section>

    <div class="grid grid-cols-4 gap-2 sm:gap-3 mb-6">
        <a href="{{ route('study.public') }}" class="card !px-1 !py-3 sm:!p-4 text-center flex flex-col items-center justify-center overflow-hidden"><div class="text-xl sm:text-2xl leading-none">📚</div><div class="text-[11px] sm:text-sm mt-1 font-semibold leading-tight w-full truncate">Kajian</div></a>
        <a href="{{ route('qurban.index') }}" class="card !px-1 !py-3 sm:!p-4 text-center flex flex-col items-center justify-center overflow-hidden"><div class="text-xl sm:text-2xl leading-none">🐐</div><div class="text-[11px] sm:text-sm mt-1 font-semibold leading-tight w-full truncate">Qurban</div></a>
        <a href="{{ route('sadaqah') }}" class="card !px-1 !py-3 sm:!p-4 text-center flex flex-col items-center justify-center overflow-hidden"><div class="text-xl sm:text-2xl leading-none">💚</div><div class="text-[11px] sm:text-sm mt-1 font-semibold leading-tight w-full truncate">Sedekah</div></a>
        <a href="{{ route('finance.public') }}" class="card !px-1 !py-3 sm:!p-4 text-center flex flex-col items-center justify-center overflow-hidden"><div class="text-xl sm:text-2xl leading-none">📊</div><div class="text-[11px] sm:text-sm mt-1 font-semibold leading-tight w-full truncate">Keuangan</div></a>
    </div>

    <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-bold">Jadwal Sholat Hari Ini</h2>
        <a href="{{ route('prayer.index') }}" class="text-sm text-accent font-semibold">Lihat semua →</a>
    </div>
    <div class="card grid grid-cols-3 gap-3 text-center mb-6">
        @forelse($prayer['times'] as $name => $time)
            <div class="rounded-xl bg-surface-2 py-2">
                <div class="text-xs text-muted">{{ $name }}</div>
                <div class="font-mono font-bold">{{ $time }}</div>
            </div>
        @empty
            <p class="col-span-3 text-muted text-sm">{{ $prayer['note'] ?? 'Jadwal belum tersedia.' }}</p>
        @endforelse
    </div>

    <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-bold">Kajian Mendatang</h2>
        <a href="{{ route('study.public') }}" class="text-sm text-accent font-semibold">Semua →</a>
    </div>
    @forelse($upcoming as $k)
        <a href="{{ route('study.show', $k) }}" class="card flex items-center gap-3 mb-3">
            <div class="text-2xl">📖</div>
            <div>
                <div class="font-semibold">{{ $k->title }}</div>
                <div class="text-sm text-muted">{{ $k->speaker }} • {{ $k->scheduled_at->translatedFormat('d M Y, H:i') }}</div>
            </div>
        </a>
    @empty
        <p class="text-muted text-sm">Belum ada kajian terjadwal.</p>
    @endforelse

    <script>
        function prayerCountdown(times) {
            return {
                nextName: '—', nextTime: '', countdown: '',
                init() { this.tick(); setInterval(() => this.tick(), 1000); },
                tick() {
                    const now = new Date();
                    let next = null;
                    for (const [name, t] of Object.entries(times)) {
                        if (!t) continue;
                        const [h, m] = t.split(':').map(Number);
                        const d = new Date(now); d.setHours(h, m, 0, 0);
                        if (d > now && (!next || d < next.d)) next = { name, t, d };
                    }
                    if (!next) {
                        const first = Object.entries(times).find(([, v]) => v);
                        if (first) { this.nextName = first[0]; this.nextTime = first[1]; this.countdown = 'Besok'; }
                        return;
                    }
                    this.nextName = next.name; this.nextTime = next.t;
                    const diff = Math.max(0, next.d - now);
                    const hh = String(Math.floor(diff / 3.6e6)).padStart(2, '0');
                    const mm = String(Math.floor((diff % 3.6e6) / 6e4)).padStart(2, '0');
                    const ss = String(Math.floor((diff % 6e4) / 1e3)).padStart(2, '0');
                    this.countdown = `menuju ${next.name} dalam ${hh}:${mm}:${ss}`;
                }
            };
        }
    </script>
@endsection
