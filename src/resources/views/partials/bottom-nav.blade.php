@php
    $current = Route::currentRouteName();
    $tabs = [
        ['home', '🏠', 'Beranda'],
        ['prayer.index', '🕌', 'Sholat'],
        ['study.public', '📚', 'Kajian'],
        ['qurban.index', '🐐', 'Qurban'],
        [auth()->check() ? 'user.qurban' : 'login', '👤', 'Profil'],
    ];
@endphp
<nav class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-emerald-100 z-30 pb-safe pl-safe pr-safe shadow-[0_-1px_8px_rgba(0,0,0,0.04)]">
    <div class="max-w-3xl mx-auto grid grid-cols-5 text-center text-xs">
        @foreach($tabs as [$route, $icon, $label])
            @php($active = $current === $route)
            <a href="{{ route($route) }}"
               class="py-1.5 flex flex-col items-center gap-0.5 transition-colors {{ $active ? 'text-primary font-semibold' : 'text-muted hover:text-primary/70' }}">
                <span class="text-lg leading-none w-10 h-7 flex items-center justify-center rounded-full transition-colors {{ $active ? 'bg-surface-2' : '' }}">{{ $icon }}</span>
                <span class="leading-tight">{{ $label }}</span>
            </a>
        @endforeach
    </div>
</nav>
