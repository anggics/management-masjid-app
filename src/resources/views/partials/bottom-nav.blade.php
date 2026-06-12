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
<nav class="fixed bottom-0 inset-x-0 bg-white border-t z-30">
    <div class="max-w-3xl mx-auto grid grid-cols-5 text-center text-xs">
        @foreach($tabs as [$route, $icon, $label])
            @php($active = $current === $route)
            <a href="{{ route($route) }}"
               class="py-2 flex flex-col items-center gap-0.5 {{ $active ? 'text-primary font-semibold border-t-2 border-primary -mt-px' : 'text-muted' }}">
                <span class="text-lg">{{ $icon }}</span>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </div>
</nav>
