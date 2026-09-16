@extends('layouts.app')
@section('title', __('Service categories'))
@section('header-actions')
    <a href="{{ route('categories.create') }}" class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">+ {{ __('New category') }}</a>
@endsection
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 divide-y divide-ink-900/5">
        @forelse ($categories as $category)
            <div class="flex items-center justify-between px-5 py-3.5">
                <div>
                    <p class="text-sm font-medium text-ink-900">{{ $category->name }}</p>
                    <p class="text-xs text-ink-900/50">{{ $category->description }} · {{ $category->service_requests_count }} {{ __('tickets') }}</p>
                </div>
                <div class="flex gap-3 text-sm">
                    <a href="{{ route('categories.edit', $category) }}" class="text-ink-900/60 hover:text-ink-900">{{ __('Edit') }}</a>
                    <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit='return confirm(@json(__('Delete this category?')))'>
                        @csrf @method('DELETE')
                        <button class="text-rust-600 hover:underline">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-center text-sm text-ink-900/40">{{ __('No categories yet.') }}</p>
        @endforelse
    </div>
@endsection
