<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=Kantumruy+Pro:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: {
                            900: '#161D26',
                            800: '#1C2530'
                        },
                        paper: '#F6F4EF',
                        rust: {
                            DEFAULT: '#BF5B2E',
                            600: '#A64B22'
                        }
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', '"Kantumruy Pro"', 'sans-serif'],
                        sans: ['Inter', '"Kantumruy Pro"', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-ink-900 font-sans antialiased min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        @php($brand = \App\Models\BusinessSetting::firstOrCreate([], ['business_name' => 'ServiceHub']))
        <div class="flex justify-center mb-4">
            @include('partials.language-switcher', ['variant' => 'dark'])
        </div>
        <div class="text-center mb-8">
            <p class="font-display text-2xl font-bold text-white tracking-tight">{{ $brand->business_name ?? 'ServiceHub' }}</p>
            <p class="text-paper/40 text-sm mt-1">@yield('subtitle', 'Service & maintenance desk')</p>
        </div>
        <div class="bg-paper rounded-lg shadow-xl p-8">
            @if ($errors->any())
                <div class="mb-5 rounded-md border border-rust/30 bg-rust/10 px-4 py-3 text-sm text-rust-600">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</body>

</html>
