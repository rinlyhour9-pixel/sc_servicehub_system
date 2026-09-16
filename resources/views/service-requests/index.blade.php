@extends('layouts.app')
@section('title', __('Services'))
@section('header-actions')
    <a href="{{ route('service-requests.create') }}"
        class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">+ {{ __('New service') }}</a>
@endsection
@section('content')
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search service, title, customer...') }}"
            class="rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm flex-1 min-w-[200px]">
        <select name="status" class="rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            <option value="">{{ __('All statuses') }}</option>
            @foreach (\App\Models\ServiceRequest::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ __(ucwords(str_replace('_', ' ', $status))) }}
                </option>
            @endforeach
        </select>
        <select name="priority" class="rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            <option value="">{{ __('All priorities') }}</option>
            @foreach (\App\Models\ServiceRequest::PRIORITIES as $priority)
                <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ __(ucfirst($priority)) }}</option>
            @endforeach
        </select>
        <select name="technician_id" class="rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            <option value="">{{ __('All technicians') }}</option>
            @foreach ($technicians as $tech)
                <option value="{{ $tech->id }}" @selected((string) request('technician_id') === (string) $tech->id)>{{ $tech->name }}</option>
            @endforeach
        </select>
        <button class="border border-ink-900/20 text-ink-900 text-sm rounded-md px-4 py-2 hover:bg-white">{{ __('Filter') }}</button>
    </form>

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Service') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Customer') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Technician') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Priority') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Status') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Scheduled') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($serviceRequests as $sr)
                    <tr class="border-t border-ink-900/5 hover:bg-paper cursor-pointer"
                        onclick="window.location='{{ route('service-requests.show', $sr) }}'">
                        <td class="px-5 py-3">
                            <p class="font-medium text-ink-900">{{ $sr->title }}</p>
                            <p class="text-xs text-ink-900/40 font-mono">{{ $sr->ticket_number }}</p>
                        </td>
                        <td class="px-5 py-3 text-ink-900/70">{{ $sr->customer->name }}</td>
                        <td class="px-5 py-3 text-ink-900/70">{{ $sr->technician->name ?? '—' }}</td>
                        <td class="px-5 py-3"><span
                                class="text-xs px-2 py-0.5 rounded-full {{ $sr->priorityBadgeColor() }}">{{ __(ucfirst($sr->priority)) }}</span>
                        </td>
                        <td class="px-5 py-3"><span
                                class="text-xs px-2 py-0.5 rounded-full {{ $sr->statusBadgeColor() }}">{{ __($sr->statusLabel()) }}</span>
                        </td>
                        <td class="px-5 py-3 text-ink-900/60">
                            {{ optional($sr->scheduled_at)->format('M j, g:i A') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-6 text-center text-ink-900/40">{{ __('No services found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $serviceRequests->links() }}</div>
@endsection
