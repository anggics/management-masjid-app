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
        <aside class="w-64 bg-primary text-white min-h-screen p-4 hidden md:block">
            <a href="{{ route('home') }}" class="font-bold text-lg flex items-center gap-2 mb-6">
                @if(($mosque ?? null) && $mosque->logo_url)
                    <img src="{{ $mosque->logo_url }}" alt="Logo" class="w-7 h-7 rounded-full object-cover bg-white shrink-0">
                @else
                    <span class="w-7 h-7 rounded-full bg-gold/90 flex items-center justify-center text-primary shrink-0 text-sm">☪</span>
                @endif
                {{ $mosque->name ?? config('app.name') }}
            </a>
            <nav class="space-y-1">
                @foreach($nav as [$route, $label, $onlyAdmin])
                    @if(!$onlyAdmin || auth()->user()->isAdmin())
                        @php($active = request()->routeIs($route))
                        <a href="{{ route($route) }}" class="block px-3 py-2 rounded-lg {{ $active ? 'bg-white/15 font-semibold' : 'hover:bg-white/10' }}">{{ $label }}</a>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="bg-white border-b px-4 py-3 flex items-center justify-between sticky top-0 z-20">
                <button class="md:hidden" @click="open = !open">☰</button>
                <h1 class="font-bold">@yield('title', 'Panel')</h1>
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-muted">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-primary font-semibold">Keluar</button></form>
                </div>
            </header>

            {{-- Mobile nav --}}
            <nav x-show="open" x-cloak class="md:hidden bg-primary text-white p-3 space-y-1">
                @foreach($nav as [$route, $label, $onlyAdmin])
                    @if(!$onlyAdmin || auth()->user()->isAdmin())
                        <a href="{{ route($route) }}" class="block px-3 py-2 rounded-lg hover:bg-white/10">{{ $label }}</a>
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
