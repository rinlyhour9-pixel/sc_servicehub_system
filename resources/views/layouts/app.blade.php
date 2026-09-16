<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: {
                            DEFAULT: '#1C2530',
                            900: '#161D26',
                            800: '#1C2530'
                        },
                        paper: '#F6F4EF',
                        rust: {
                            DEFAULT: '#BF5B2E',
                            600: '#A64B22',
                            700: '#8A3D1B'
                        },
                        moss: '#3F7D58',
                        mustard: '#C99A2E'
                    },
                    fontFamily: {
                        display: ['Poppins', '"Kantumruy Pro"', 'sans-serif'],
                        sans: ['Poppins', '"Kantumruy Pro"', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
    @stack('head')
</head>

<body class="bg-slate-100 text-ink-900 font-sans antialiased">
    {{-- Forces the Tailwind CDN build to pre-generate these width utilities.
         Alpine's :class only swaps classes at runtime — it never adds a class
         the CDN script hasn't already seen in a plain class="" attribute, so
         without this the sidebar collapse silently does nothing. --}}
    <div class="hidden w-64 w-24" aria-hidden="true"></div>
    @php
        $navItem = function (string $route, bool $compact = false): string {
            $isActive =
                request()->routeIs($route) || request()->routeIs($route . '.*') || request()->routeIs($route . '*');

            if ($compact) {
                return 'group flex w-full items-center justify-start rounded-lg py-3 mb-1 transition text-left ' .
                    ($isActive
                        ? 'bg-amber-400 text-[#342f73] font-semibold shadow-sm ring-1 ring-amber-200'
                        : 'text-white/80');
            }

            return 'group flex w-full items-center justify-start gap-3 rounded-lg px-3 py-2.5 mb-1 transition text-left ' .
                ($isActive
                    ? 'bg-amber-400 text-[#342f73] font-semibold shadow-sm ring-1 ring-amber-200'
                    : 'text-white/80');
        };
        $icon = 'h-6 w-6 shrink-0 text-current';
    @endphp
    <div x-data="{ sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true') }"
        x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', JSON.stringify(value)))"
        class="min-h-screen flex flex-col lg:flex-row">

        <!-- Sidebar (non-responsive) -->
        <aside
            class="sidebar flex flex-col items-stretch bg-[#45418f] text-white shadow-xl transition-all duration-300 ease-in-out min-h-screen overflow-hidden"
            :class="sidebarOpen ? 'w-64' : 'w-24'" style="transition: width .2s ease">
            <div class="px-4 py-4 border-b border-white/15 sm:px-6 sm:py-6">
                @php($brand = \App\Models\BusinessSetting::firstOrCreate([], ['business_name' => 'ServiceHub']))
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 font-display font-semibold text-lg tracking-tight min-w-0">
                    @if ($brand->logo_path)
                        <img src="{{ asset('storage/' . $brand->logo_path) }}"
                            alt="{{ $brand->business_name ?? 'ServiceHub' }}"
                            class="h-9 w-9 rounded-lg object-cover bg-white/5 border border-white/10 shrink-0">
                    @endif
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ $brand->business_name ?? 'ServiceHub' }}</span>
                </a>
            </div>
            <nav class="flex-1 px-2 py-4 text-sm overflow-y-auto sm:px-3 sm:py-5">
                @include('layouts._sidebar_content', ['compact' => true])
            </nav>
            <div class="px-4 py-4 border-t border-white/15 text-sm mt-auto">
                @auth
                    <p class="px-3 text-white font-medium">{{ auth()->user()->name }}</p>
                    <p class="px-3 text-white/55 text-xs mb-2 capitalize">{{ __('Administrator') }}</p>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button
                            class="w-full text-left px-3 py-2 rounded-lg text-white/75">{{ __('Log out') }}</button></form>
                @endauth
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0">
            <header
                class="bg-white border-b border-ink-900/10 px-4 py-3 flex flex-col gap-3 sm:px-6 lg:px-8 lg:py-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <button id="sidebarToggle" type="button" @click="sidebarOpen = !sidebarOpen"
                        :aria-expanded="sidebarOpen" aria-label="Toggle navigation"
                        title="Toggle navigation (Ctrl+B)"
                        class="shrink-0 flex items-center justify-center h-10 w-10 rounded-md border-2 border-rust text-rust hover:bg-rust/5 transition">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="7" x2="20" y2="7" />
                            <line x1="4" y1="12" x2="20" y2="12" />
                            <line x1="4" y1="17" x2="20" y2="17" />
                        </svg>
                    </button>
                    <h1 class="font-display text-xl font-semibold text-ink-900">@yield('title', __('Dashboard'))</h1>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                    @include('partials.language-switcher', ['variant' => 'light'])
                    @yield('header-actions')
                </div>
            </header>
            <main class="flex-1 px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-6">
                @if (session('status'))
                    <div class="mb-5 rounded-md border border-moss/30 bg-moss/10 px-4 py-3 text-sm text-moss shadow-sm">
                        {{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-5 rounded-md border border-rust/30 bg-rust/10 px-4 py-3 text-sm text-rust-700 shadow-sm">
                        {{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-rust/30 bg-rust/10 px-4 py-3 text-sm text-rust-700">
                        <p class="font-medium mb-1">{{ __('Please fix the following:') }}</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @auth
        <script>
            let shownAlerts = new Set();
            async function checkServiceAlerts() {
                if (!('Notification' in window) || Notification.permission !== 'granted') return;
                const alerts = await fetch('{{ route('notifications.unread') }}', {
                    headers: {
                        Accept: 'application/json'
                    }
                }).then(response => response.ok ? response.json() : []);
                alerts.forEach(alert => {
                    if (!shownAlerts.has(alert.id)) {
                        shownAlerts.add(alert.id);
                        const notification = new Notification('ServiceHub', {
                            body: alert.message
                        });
                        notification.onclick = () => window.location = alert.url;
                    }
                });
            }
            checkServiceAlerts();
            setInterval(checkServiceAlerts, 60000);
        </script>
    @endauth
    <script src="{{ asset('assets/app.js') }}" defer></script>
</body>

</html>
