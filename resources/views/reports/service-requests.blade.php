@extends('layouts.app')
@section('title', __('Service Requests Report'))
@section('content')
    @include('reports._filters')

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total tickets') }}</p>
            <p class="font-display text-2xl font-semibold text-ink-900">{{ $requests->count() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Completed') }}</p>
            <p class="font-display text-2xl font-semibold text-moss">{{ $byStatus->get('completed', 0) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Cancelled') }}</p>
            <p class="font-display text-2xl font-semibold text-rust">{{ $byStatus->get('cancelled', 0) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Avg. time to complete') }}</p>
            <p class="font-display text-2xl font-semibold text-mustard">{{ $avgHours !== null ? $avgHours . ' ' . __('hrs') : '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold mb-4">{{ __('By status') }}</h2>
            @forelse (\App\Models\ServiceRequest::STATUSES as $status)
                <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0">
                    <span class="text-sm text-ink-900/70">{{ __(ucwords(str_replace('_', ' ', $status))) }}</span>
                    <span class="text-sm font-semibold">{{ $byStatus->get($status, 0) }}</span>
                </div>
            @empty
            @endforelse
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold mb-4">{{ __('By priority') }}</h2>
            @foreach (\App\Models\ServiceRequest::PRIORITIES as $priority)
                <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0">
                    <span class="text-sm text-ink-900/70">{{ __(ucfirst($priority)) }}</span>
                    <span class="text-sm font-semibold">{{ $byPriority->get($priority, 0) }}</span>
                </div>
            @endforeach
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold mb-4">{{ __('By category') }}</h2>
            @forelse ($byCategory as $name => $count)
                <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0">
                    <span class="text-sm text-ink-900/70">{{ $name }}</span>
                    <span class="text-sm font-semibold">{{ $count }}</span>
                </div>
            @empty
                <p class="text-sm text-ink-900/40">{{ __('No tickets in this range.') }}</p>
            @endforelse
        </div>
    </div>
@endsection
