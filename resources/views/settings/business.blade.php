@extends('layouts.app')
@section('title', __('Business Identity'))
@section('content')
    <form method="POST" action="{{ route('business-settings.update') }}" enctype="multipart/form-data"
        class="max-w-xl bg-white rounded-lg border border-ink-900/10 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Business name') }}</label>
            <input name="business_name" value="{{ old('business_name', $setting->business_name) }}"
                class="w-full rounded-md border-ink-900/20">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Brand image') }}</label>
            @if ($setting->logo_path)
                <img src="{{ asset('storage/' . $setting->logo_path) }}"
                    class="h-16 w-16 object-cover rounded-lg border border-ink-900/10 mb-2">
            @endif
            <input type="file" name="logo" accept="image/*" class="text-sm">
            <p class="text-xs text-slate-500 mt-1">{{ __('Upload a square image for the sidebar brand logo.') }}</p>
        </div>

        <button class="bg-rust text-white rounded-md px-4 py-2 text-sm">{{ __('Save branding') }}</button>
    </form>
@endsection
