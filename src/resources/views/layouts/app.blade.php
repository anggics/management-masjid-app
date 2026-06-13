<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1B4332">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink font-sans min-h-screen pb-nav overflow-x-hidden">
    <header class="bg-gradient-to-r from-primary to-primary-light text-white sticky top-0 z-30 shadow-md pt-safe pl-safe pr-safe">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold min-w-0 flex-1">
                @if(($mosque ?? null) && $mosque->logo_url)
                    <img src="{{ $mosque->logo_url }}" alt="Logo" class="w-8 h-8 rounded-full object-cover bg-white shrink-0">
                @else
                    <span class="w-8 h-8 rounded-full bg-gold/90 flex items-center justify-center text-primary shrink-0">☪</span>
                @endif
                <!-- <img src="{{ $mosque->logo_url }}" alt="Logo" class="w-8 h-8 rounded-full object-cover bg-white shrink-0"> -->
                <span class="truncate">{{ $mosque->name ?? config('app.name') }}</span>
            </a>
            <nav class="flex items-center gap-2 text-sm shrink-0">
                @auth
                    @if(auth()->user()->hasRole('admin','staff'))
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-full bg-white/15 hover:bg-white/25 transition font-semibold">Panel</a>
                    @endif
                    <a href="{{ route('user.notifications') }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/25 transition" aria-label="Notifikasi">🔔</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="px-3 py-1.5 rounded-full bg-white/15 hover:bg-white/25 transition font-semibold">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full bg-white text-primary hover:bg-white/90 transition font-semibold">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-5">
        @include('partials.flash')
        @yield('content')
    </main>

    @include('partials.bottom-nav')
</body>
</html>
