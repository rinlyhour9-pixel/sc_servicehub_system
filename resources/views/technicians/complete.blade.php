@extends('layouts.app')
@section('title', __('Technician — Completed Work'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6">
        <h2 class="font-display text-lg font-semibold mb-4">{{ __('Technician Completed Work') }}</h2>
        <div class="space-y-4">
            @foreach ($technicians as $t)
                <div class="border rounded">
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $t->name }}</div>
                            <div class="text-xs text-ink-900/60">{{ $t->phone ?? __('No phone') }}</div>
                        </div>
                        <div class="text-sm text-moss font-semibold">{{ $t->completed_count }} {{ __('done') }}</div>
                    </div>
                    @if (!empty($t->recent_completed) && $t->recent_completed->isNotEmpty())
                        <div class="p-3 bg-paper">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($t->recent_completed as $r)
                                    <li>
                                        <a href="{{ route('service-requests.show', $r) }}"
                                            class="font-medium">{{ $r->ticket_number }}</a>
                                        — {{ $r->title }} <span
                                            class="text-xs text-ink-900/60">({{ $r->customer->name }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="p-3 text-sm text-ink-900/60">{{ __('No completed requests') }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
