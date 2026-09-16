<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: { DEFAULT: '#1C2530', 900: '#161D26', 800: '#1C2530' },
                        paper: '#F6F4EF',
                        rust: { DEFAULT: '#BF5B2E', 600: '#A64B22', 700: '#8A3D1B' },
                        moss: '#3F7D58',
                        mustard: '#C99A2E'
                    },
                    fontFamily: { display: ['Poppins', '"Kantumruy Pro"', 'sans-serif'], sans: ['Poppins', '"Kantumruy Pro"', 'sans-serif'] }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-100 text-ink-900 font-sans antialiased min-h-screen flex flex-col">
    @php($brand = \App\Models\BusinessSetting::firstOrCreate([], ['business_name' => 'ServiceHub']))
    <header class="bg-[#45418f] text-white">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 font-display font-semibold">
                @if ($brand->logo_path)
                    <img src="{{ asset('storage/' . $brand->logo_path) }}" class="h-7 w-7 rounded object-cover">
                @endif
                <span>{{ $brand->business_name ?? 'ServiceHub' }}</span>
                <span class="text-white/50 font-normal text-sm">· @yield('portal-name')</span>
            </div>
            <div class="flex items-center gap-4">
                @include('partials.language-switcher', ['variant' => 'dark'])
                <span class="text-sm text-white/80">{{ $portalUser->name ?? '' }}</span>
                <form method="POST" action="@yield('logout-route')">
                    @csrf
                    <button class="text-sm bg-white/10 hover:bg-white/20 rounded-md px-3 py-1.5 transition">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </header>
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-5 rounded-md border border-moss/30 bg-moss/10 px-4 py-3 text-sm text-moss shadow-sm">
                {{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-md border border-rust/30 bg-rust/10 px-4 py-3 text-sm text-rust-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>

</html>
