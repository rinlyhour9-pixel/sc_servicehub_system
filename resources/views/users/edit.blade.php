@extends('layouts.app')
@section('title', __('Edit User'))
@section('content')
    <form method="POST" action="{{ route('users.update', $user) }}"
        class="max-w-xl bg-white rounded-lg border border-ink-900/10 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div><label class="block text-sm font-medium mb-1">{{ __('Name') }}</label><input name="name"
                value="{{ old('name', $user->name) }}" required class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Phone number (used to sign in)') }}</label><input name="phone"
                value="{{ old('phone', $user->phone) }}" required class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Email (optional)') }}</label><input type="email" name="email"
                value="{{ old('email', $user->email) }}" class="w-full rounded-md border-ink-900/20 px-3 py-2">
        </div>

        <div><label class="block text-sm font-medium mb-1">{{ __('Password — leave blank to keep current') }}</label><input
                type="password" name="password" class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Confirm password') }}</label><input type="password"
                name="password_confirmation" class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <button class="bg-rust text-white rounded-md px-4 py-2 text-sm font-medium">{{ __('Save changes') }}</button>
    </form>
@endsection
