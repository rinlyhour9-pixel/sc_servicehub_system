@extends('layouts.app')
@section('title', __('Sales & Service History'))
@section('content')
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6 bg-white rounded-lg border border-ink-900/10 p-4">
        <div>
            <label class="block text-xs text-ink-900/50 mb-1">{{ __('From') }}</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="rounded-md border-ink-900/20 text-sm">
        </div>
        <div>
            <label class="block text-xs text-ink-900/50 mb-1">{{ __('To') }}</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="rounded-md border-ink-900/20 text-sm">
        </div>
        <div>
            <label class="block text-xs text-ink-900/50 mb-1">{{ __('Customer') }}</label>
            <select name="customer_id" class="rounded-md border-ink-900/20 text-sm">
                <option value="">{{ __('All customers') }}</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-ink-900/50 mb-1">{{ __('Technician') }}</label>
            <select name="technician_id" class="rounded-md border-ink-900/20 text-sm">
                <option value="">{{ __('All technicians') }}</option>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}" @selected(request('technician_id') == $tech->id)>{{ $tech->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-ink-900/50 mb-1">{{ __('Status') }}</label>
            <select name="status" class="rounded-md border-ink-900/20 text-sm">
                <option value="">{{ __('All statuses') }}</option>
                @foreach (\App\Models\ServiceRequest::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst(str_replace('_', ' ', $status))) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs text-ink-900/50 mb-1">{{ __('Search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Ticket, title, or customer') }}"
                class="w-full rounded-md border-ink-900/20 text-sm">
        </div>
        <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2">{{ __('Apply') }}</button>
        <a href="{{ route('reports.history') }}" class="text-xs px-3 py-1.5 rounded-md border border-ink-900/15 hover:bg-paper">{{ __('Reset') }}</a>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Jobs in range') }}</p>
            <p class="font-display text-2xl font-semibold text-ink-900">{{ $records->total() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Completed') }}</p>
            <p class="font-display text-2xl font-semibold text-moss">{{ $completedCount }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total revenue') }}</p>
            <p class="font-display text-2xl font-semibold text-ink-900">${{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Date') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Ticket') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Customer') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Technician') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Completed?') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Amount') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Payment') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $sr)
                    <tr class="border-t border-ink-900/5 hover:bg-paper cursor-pointer"
                        onclick="window.location='{{ route('service-requests.show', $sr) }}'">
                        <td class="px-5 py-3 text-ink-900/60">{{ $sr->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs text-ink-900/70">{{ $sr->ticket_number }}</p>
                            <p class="text-ink-900">{{ $sr->title }}</p>
                        </td>
                        <td class="px-5 py-3">{{ $sr->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $sr->technician->name ?? __('Unassigned') }}</td>
                        <td class="px-5 py-3">
                            @if ($sr->status === \App\Models\ServiceRequest::STATUS_COMPLETED)
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg>
                                    {{ __('Completed') }}
                                </span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $sr->statusBadgeColor() }}">{{ __($sr->statusLabel()) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold">
                            {{ $sr->invoice ? '$'.number_format($sr->invoice->total(), 2) : '—' }}
                        </td>
                        <td class="px-5 py-3">
                            @if ($sr->invoice)
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $sr->invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($sr->invoice->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">{{ __(ucfirst($sr->invoice->status)) }}</span>
                            @else
                                <span class="text-xs text-ink-900/40">{{ __('No invoice') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-6 text-center text-ink-900/40">{{ __('No jobs match these filters.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $records->links() }}</div>
@endsection
