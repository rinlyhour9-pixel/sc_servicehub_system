@extends('layouts.guest')
@section('title', 'Register')
@section('content')
    <h2 class="font-display text-lg font-semibold text-ink-900 mb-5">{{ __('Create a staff account') }}</h2>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Password') }}</label>
            <input type="password" name="password" required
                   class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Confirm password') }}</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
        </div>
        <p class="text-xs text-ink-900/50">{{ __('New accounts are created with the "staff" role. An admin can promote you to technician or admin afterward.') }}</p>
        <button type="submit" class="w-full bg-rust hover:bg-rust-600 text-white font-medium rounded-md py-2.5 text-sm transition">
            {{ __('Create account') }}
        </button>
    </form>
    <p class="text-sm text-ink-900/60 mt-5 text-center">
        {{ __('Already have an account?') }} <a href="{{ route('login') }}" class="text-rust font-medium">{{ __('Sign in') }}</a>
    </p>
@endsection
