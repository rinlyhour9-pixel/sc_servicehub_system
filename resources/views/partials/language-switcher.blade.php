@php
    $isDark = ($variant ?? 'light') === 'dark';
    $activeClass = $isDark ? 'bg-white/20 text-white' : 'bg-rust/10 text-rust';
    $inactiveClass = $isDark ? 'text-white/50 hover:text-white/80' : 'text-ink-900/40 hover:text-ink-900/70';
@endphp
<div class="inline-flex items-center rounded-md {{ $isDark ? 'bg-white/10' : 'bg-ink-900/5' }} p-0.5 text-xs font-medium">
    <form method="POST" action="{{ route('locale.switch', 'en') }}">
        @csrf
        <button type="submit" class="rounded px-2 py-1 transition {{ app()->getLocale() === 'en' ? $activeClass : $inactiveClass }}">EN</button>
    </form>
    <form method="POST" action="{{ route('locale.switch', 'km') }}">
        @csrf
        <button type="submit" class="rounded px-2 py-1 transition {{ app()->getLocale() === 'km' ? $activeClass : $inactiveClass }}">ខ្មែរ</button>
    </form>
</div>
