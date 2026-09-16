@extends('layouts.portal')
@section('title', __('My Jobs'))
@section('portal-name', __('Technician Portal'))
@section('logout-route', route('technician.logout'))
@php($portalUser = $technician)
@section('content')
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Open jobs') }}</p>
            <p class="font-display text-2xl font-semibold text-rust">{{ $openJobs->count() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Completed') }}</p>
            <p class="font-display text-2xl font-semibold text-moss">{{ $completedJobs->count() }}</p>
        </div>
    </div>

    <h2 class="font-display font-semibold text-ink-900 mb-3">{{ __('My open jobs') }}</h2>
    <div class="space-y-3 mb-8">
        @forelse ($openJobs as $job)
            <div class="bg-white rounded-lg border border-ink-900/10 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium text-ink-900">{{ $job->title }}</p>
                        <p class="text-xs text-ink-900/40 font-mono">{{ $job->ticket_number }}</p>
                        <p class="text-sm text-ink-900/60 mt-1">{{ $job->customer->name }} ·
                            {{ $job->customer->phone }}</p>
                        @if ($job->service_address)
                            <p class="text-xs text-ink-900/50">{{ $job->service_address }}</p>
                        @endif
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $job->statusBadgeColor() }}">{{ __($job->statusLabel()) }}</span>
                </div>

                <div class="mt-3 flex items-center gap-2">
                    <form method="POST" action="{{ route('technician.jobs.update-status', $job) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="text-sm rounded-md border-ink-900/20">
                            @foreach (\App\Models\ServiceRequest::STATUSES as $status)
                                <option value="{{ $status }}" @selected($job->status === $status)>
                                    {{ __(ucwords(str_replace('_', ' ', $status))) }}</option>
                            @endforeach
                        </select>
                        <button class="bg-ink-800 hover:bg-ink-700 text-white text-xs font-medium rounded-md px-3 py-1.5">{{ __('Update') }}</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('technician.jobs.notes.store', $job) }}" class="mt-3 flex items-center gap-2">
                    @csrf
                    <input name="body" placeholder="{{ __('Add a note...') }}" required
                        class="flex-1 text-sm rounded-md border-ink-900/20">
                    <button class="bg-paper border border-ink-900/15 text-ink-900 text-xs font-medium rounded-md px-3 py-1.5">{{ __('Add note') }}</button>
                </form>

                @if ($job->notes->isNotEmpty())
                    <div class="mt-3 space-y-1 border-t border-ink-900/5 pt-2">
                        @foreach ($job->notes->take(3) as $note)
                            <p class="text-xs text-ink-900/50">{{ $note->body }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-ink-900/40">{{ __('No open jobs assigned to you.') }}</p>
        @endforelse
    </div>

    <h2 class="font-display font-semibold text-ink-900 mb-3">{{ __('Recently completed') }}</h2>
    <div class="space-y-2">
        @forelse ($completedJobs->take(10) as $job)
            <div class="bg-white rounded-lg border border-ink-900/10 p-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink-900">{{ $job->title }}</p>
                    <p class="text-xs text-ink-900/40">{{ $job->customer->name }}</p>
                </div>
                <span class="text-xs text-ink-900/40">{{ optional($job->completed_at)->format('M j, Y') }}</span>
            </div>
        @empty
            <p class="text-sm text-ink-900/40">{{ __('No completed jobs yet.') }}</p>
        @endforelse
    </div>
@endsection
