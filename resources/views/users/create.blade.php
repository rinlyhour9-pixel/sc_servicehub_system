@extends('layouts.app')
@section('title', __('Add User'))
@section('content')
    <form method="POST" action="{{ route('users.store') }}"
        class="max-w-xl bg-white rounded-lg border border-ink-900/10 p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium mb-1">{{ __('Name') }}</label><input name="name" value="{{ old('name') }}"
                required class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Phone number (used to sign in)') }}</label><input name="phone"
                value="{{ old('phone') }}" required class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Email (optional)') }}</label><input type="email" name="email"
                value="{{ old('email') }}" class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Password') }}</label><input type="password" name="password" required
                class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Confirm password') }}</label><input type="password"
                name="password_confirmation" required class="w-full rounded-md border-ink-900/20"></div>
        <button class="bg-rust text-white rounded-md px-4 py-2 text-sm font-medium">{{ __('Create account') }}</button>
    </form>
@endsection
