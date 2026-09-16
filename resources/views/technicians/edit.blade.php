@extends('layouts.app')
@section('title', __('Edit Technician'))
@section('content')
    <form method="POST" action="{{ route('technicians.update', $technician) }}"
        class="max-w-xl bg-white rounded-lg border border-ink-900/10 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div><label class="block text-sm font-medium mb-1">{{ __('Name') }}</label><input name="name"
                value="{{ old('name', $technician->name) }}" required class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Phone number (used to sign in)') }}</label><input name="phone"
                value="{{ old('phone', $technician->phone) }}" required class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Email (optional)') }}</label><input type="email" name="email"
                value="{{ old('email', $technician->email) }}" class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>

        <div><label class="block text-sm font-medium mb-1">{{ __('Password — leave blank to keep current') }}</label>
            <input type="password" name="password" class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">{{ __('Confirm password') }}</label>
            <input type="password" name="password_confirmation" class="w-full rounded-md border-ink-900/20 px-3 py-2"></div>

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Services') }}</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach ($categories as $c)
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="service_categories[]"
                            value="{{ $c->id }}" @checked(in_array($c->id, old('service_categories', $technician->serviceCategories->pluck('id')->toArray()))) class="rounded border-ink-900/20">
                        <span class="text-sm">{{ $c->name }}</span></label>
                @endforeach
            </div>
        </div>

        <button class="bg-rust text-white rounded-md px-4 py-2 text-sm font-medium">{{ __('Save changes') }}</button>
    </form>
@endsection
