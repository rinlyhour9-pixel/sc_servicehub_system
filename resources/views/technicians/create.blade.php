@extends('layouts.app')
@section('title', __('Add Technician'))
@section('content')
    <form method="POST" action="{{ route('technicians.store') }}"
        class="max-w-xl bg-white rounded-lg border border-ink-900/10 p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium mb-1">{{ __('Name') }}</label><input name="name" value="{{ old('name') }}"
                required class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Phone number (used to sign in)') }}</label><input name="phone"
                value="{{ old('phone') }}" required class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Email (optional)') }}</label><input type="email" name="email"
                value="{{ old('email') }}" class="w-full rounded-md border-ink-900/20"></div>

        <div><label class="block text-sm font-medium mb-1">{{ __('Password — leave blank to disable login for now') }}</label>
            <input type="password" name="password" class="w-full rounded-md border-ink-900/20"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Confirm password') }}</label>
            <input type="password" name="password_confirmation" class="w-full rounded-md border-ink-900/20"></div>

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Services') }}</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach ($categories as $c)
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="service_categories[]"
                            value="{{ $c->id }}" @checked(in_array($c->id, old('service_categories', []))) class="rounded border-ink-900/20">
                        <span class="text-sm">{{ $c->name }}</span></label>
                @endforeach
            </div>
        </div>

        <button class="bg-rust text-white rounded-md px-4 py-2 text-sm font-medium">{{ __('Add technician') }}</button>
    </form>
@endsection
