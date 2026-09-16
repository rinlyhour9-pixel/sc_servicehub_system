@extends('layouts.guest')
@section('title', 'Sign in')
@section('subtitle', __('Customer sign in'))
@section('content')
    <h2 class="font-display text-2xl font-semibold text-ink-900 mb-5">{{ __('Sign In') }}</h2>
    <form method="POST" action="{{ route('client.login') }}"
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
    <p class="text-sm text-ink-900/60 mt-5 text-center">
        {{ __('New here?') }} <a href="{{ route('client.register') }}" class="text-rust font-medium">{{ __('Create an account') }}</a>
    </p>
    <p class="text-sm text-ink-900/60 mt-1 text-center">
        <a href="{{ route('login') }}" class="text-rust font-medium">{{ __('Staff sign in') }}</a>
    </p>
@endsection
