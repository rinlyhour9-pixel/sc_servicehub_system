@extends('layouts.guest')
@section('title', 'Sign in')
@section('subtitle', __('Administrator sign in'))
@section('content')
    <h2 class="font-display text-2xl font-semibold text-ink-900 mb-5">{{ __('Sign in') }}</h2>
    <form method="POST" action="{{ route('login') }}"
        class="space-y-4 bg-white p-6 rounded shadow-sm border border-ink-900/10">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Phone number') }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required autofocus
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Password') }}</label>
            <input type="password" name="password" required
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm text-ink-900/70">
            <input type="checkbox" name="remember" class="rounded border-ink-900/30 text-rust focus:ring-rust">
            {{ __('Keep me signed in') }}
        </label>
        <button type="submit"
            class="w-full bg-rust hover:bg-rust-600 text-white font-medium rounded-md py-2.5 text-sm transition shadow">{{ __('Sign in') }}</button>
    </form>
    <p class="text-sm text-ink-900/60 mt-5 text-center">{{ __('Administrator and technician accounts are created by staff.') }}</p>
    <p class="text-sm text-ink-900/60 mt-1 text-center">{{ __('Are you a customer?') }}
        <a href="{{ route('client.login') }}" class="text-rust font-medium">{{ __('Sign in here') }}</a>
    </p>
    <div class="mt-4 text-center">
        <p class="text-xs text-ink-900/40">{{ __('Demo account') }}</p>
        <div class="inline-flex items-center gap-3 mt-3 bg-ink-900/5 rounded-md px-3 py-2 text-sm">
            <div class="text-ink-900/70" id="demoPhone">012 000 001</div>
            <div class="text-ink-900/70">/</div>
            <div class="text-ink-900/70">123456</div>
            <button id="demoFill" class="ml-3 bg-rust text-white text-xs px-3 py-1 rounded shadow-sm">{{ __('Use demo credentials') }}</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('demoFill');
            if (!btn) return;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const phoneInput = document.querySelector('input[name="phone"]');
                const passInput = document.querySelector('input[name="password"]');
                if (phoneInput) phoneInput.value = document.getElementById('demoPhone').textContent.trim();
                if (passInput) passInput.value = '123456';
            });
        });
    </script>
@endsection
