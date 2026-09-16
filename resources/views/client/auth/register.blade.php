@extends('layouts.guest')
@section('title', 'Create account')
@section('subtitle', __('Customer sign up'))
@section('content')
    <h2 class="font-display text-2xl font-semibold text-ink-900 mb-5">{{ __('Create Your Account') }}</h2>
    <form method="POST" action="{{ route('client.register') }}"
        class="space-y-4 bg-white p-6 rounded shadow-sm border border-ink-900/10">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Phone number') }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Email (optional)') }}</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Password') }}</label>
            <input type="password" name="password" required
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Confirm password') }}</label>
            <input type="password" name="password_confirmation" required
                class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm px-3 py-2">
        </div>
        <button type="submit"
            class="w-full bg-rust hover:bg-rust-600 text-white font-medium rounded-md py-2.5 text-sm transition shadow">{{ __('Create account') }}</button>
    </form>
    <p class="text-sm text-ink-900/60 mt-5 text-center">
        {{ __('Already have an account?') }} <a href="{{ route('client.login') }}" class="text-rust font-medium">{{ __('Sign in') }}</a>
    </p>
@endsection
