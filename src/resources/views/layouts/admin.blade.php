<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1B4332">
    <title>@yield('title', 'Panel') — Admin {{ config('app.name') }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-ink font-sans min-h-screen" x-data="{ open: false }">
    @php($nav = [
        ['admin.dashboard', '📊 Dashboard', null],
        ['admin.profile.edit', '🕌 Profil Masjid', null],
        ['admin.qurban-types.index', '🏷 Jenis Hewan Qurban', null],
        ['admin.qurban-years.index', '📅 Tahun Qurban', null],
        ['admin.qurban.index', '🐐 Data Qurban', null],
        ['admin.committees.index', '👥 Panitia Qurban', null],
        ['admin.deposits.index', '✅ Verifikasi Setoran', null],
        ['admin.study.index', '📚 Jadwal Kajian', null],
        ['admin.prayer.index', '🕰 Jadwal Sholat', null],
        ['admin.finance.index', '💰 Laporan Keuangan', null],
        ['admin.payments.index', '💳 Metode Pembayaran', 'admin'],
        ['admin.users.index', '🧑‍💼 Pengguna', 'admin'],
    ])

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gradient-to-b from-primary to-primary-light text-white min-h-screen p-4 hidden md:flex md:flex-col sticky top-0 h-screen overflow-y-auto">
            <a href="{{ route('home') }}" class="font-bold text-lg flex items-center gap-2 mb-6 shrink-0">
                @if(($mosque ?? null) && $mosque->logo_url)
                    <img src="{{ $mosque->logo_url }}" alt="Logo" class="w-8 h-8 rounded-full object-cover bg-white shrink-0">
                @else
                    <span class="w-8 h-8 rounded-full bg-gold/90 flex items-center justify-center text-primary shrink-0 text-sm">☪</span>
                @endif
                <span class="truncate">{{ $mosque->name ?? config('app.name') }}</span>
            </a>
            <nav class="space-y-1 flex-1">
                @foreach($nav as [$route, $label, $onlyAdmin])
                    @if(!$onlyAdmin || auth()->user()->isAdmin())
                        @php($active = request()->routeIs($route))
                        <a href="{{ route($route) }}" class="flex items-center px-3 py-2 rounded-lg text-sm transition {{ $active ? 'bg-white/20 font-semibold shadow-sm border-l-4 border-gold pl-2' : 'hover:bg-white/10 text-white/90' }}">{{ $label }}</a>
                    @endif
                @endforeach
            </nav>
            <a href="{{ route('home') }}" class="mt-4 shrink-0 text-xs text-white/70 hover:text-white transition">← Kembali ke aplikasi</a>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="bg-white/95 backdrop-blur border-b border-emerald-100 px-4 py-3 flex items-center justify-between gap-2 sticky top-0 z-20">
                <div class="flex items-center gap-2 min-w-0">
                    <button class="md:hidden w-9 h-9 rounded-lg hover:bg-surface-2 flex items-center justify-center text-lg" @click="open = !open" aria-label="Menu">☰</button>
                    <h1 class="font-bold truncate">@yield('title', 'Panel')</h1>
                </div>
                <div class="flex items-center gap-3 text-sm shrink-0">
                    <span class="text-muted hidden sm:inline">{{ auth()->user()->name }}
                        <span class="ml-1 inline-block bg-surface-2 text-primary text-xs font-semibold px-2 py-0.5 rounded-full capitalize">{{ auth()->user()->role }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="px-3 py-1.5 rounded-full bg-surface-2 text-primary font-semibold hover:bg-surface transition">Keluar</button></form>
                </div>
            </header>

            {{-- Mobile nav --}}
            <nav x-show="open" x-cloak class="md:hidden bg-gradient-to-b from-primary to-primary-light text-white p-3 space-y-1 sticky top-[57px] z-20 shadow-lg">
                @foreach($nav as [$route, $label, $onlyAdmin])
                    @if(!$onlyAdmin || auth()->user()->isAdmin())
                        @php($active = request()->routeIs($route))
                        <a href="{{ route($route) }}" @click="open = false" class="block px-3 py-2 rounded-lg text-sm {{ $active ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">{{ $label }}</a>
                    @endif
                @endforeach
            </nav>

            <main class="p-4 max-w-5xl">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
