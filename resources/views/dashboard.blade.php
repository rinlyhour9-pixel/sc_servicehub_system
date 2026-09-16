@extends('layouts.app')
@section('title', __('Dashboard'))
@section('header-actions')
    <a href="{{ route('service-requests.create') }}"
        class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">
        + {{ __('New service') }}
    </a>
@endsection
@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <p class="text-xs text-ink-900/50 mb-1">{{ __('Pending') }}</p>
            <p class="font-display text-3xl font-semibold text-mustard">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <p class="text-xs text-ink-900/50 mb-1">{{ __('Assigned') }}</p>
            <p class="font-display text-3xl font-semibold text-ink-800">{{ $counts['assigned'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <p class="text-xs text-ink-900/50 mb-1">{{ __('In progress') }}</p>
            <p class="font-display text-3xl font-semibold text-rust">{{ $counts['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <p class="text-xs text-ink-900/50 mb-1">{{ __('Completed this month') }}</p>
            <p class="font-display text-3xl font-semibold text-moss">{{ $counts['completed_this_month'] }}</p>
        </div>
    </div>

    @if ($overview)
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg border border-ink-900/10 p-4">
                <p class="text-xs text-ink-900/50">{{ __('Total customers') }}</p>
                <p class="font-display text-2xl font-semibold">{{ $overview['customers'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-ink-900/10 p-4">
                <p class="text-xs text-ink-900/50">{{ __('Active technicians') }}</p>
                <p class="font-display text-2xl font-semibold text-blue-700">{{ $overview['technicians'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-ink-900/10 p-4">
                <p class="text-xs text-ink-900/50">{{ __('Unpaid invoices') }}</p>
                <p class="font-display text-2xl font-semibold text-rust">{{ $overview['unpaid_invoices'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display font-semibold text-ink-900">{{ __('Urgent jobs') }}</h2>
                    <span
                        class="rounded-full bg-red-100 text-red-700 px-2 py-1 text-[10px] font-semibold uppercase">{{ __('Priority') }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($urgentTickets as $sr)
                        <a href="{{ route('service-requests.show', $sr) }}"
                            class="block rounded-lg border border-red-100 bg-red-50/50 p-3 hover:bg-red-50 transition">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-medium text-ink-900">{{ $sr->title }}</p>
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700">{{ __(ucfirst($sr->priority)) }}</span>
                            </div>
                            <p class="mt-1 text-xs text-ink-900/60">{{ $sr->customer->name }} ·
                                {{ $sr->technician->name ?? __('Unassigned') }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-ink-900/40">{{ __('No urgent jobs right now.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h2 class="font-display font-semibold text-ink-900 mb-4">{{ __('Team performance') }}</h2>
                <div class="space-y-3">
                    @forelse ($technicianPerformance as $tech)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 p-3">
                            <div>
                                <p class="text-sm font-medium text-ink-900">{{ $tech->name }}</p>
                                <p class="text-[11px] text-ink-900/50">{{ __(':count open jobs', ['count' => $tech->open_jobs]) }}</p>
                            </div>
                            <span
                                class="text-xs font-semibold rounded-full bg-moss/10 text-moss px-2 py-1">{{ __(':count done', ['count' => $tech->completed_jobs]) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-ink-900/40">{{ __('No technicians available.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h2 class="font-display font-semibold text-ink-900 mb-4">{{ __('Service categories') }}</h2>
                <div class="space-y-3">
                    @forelse ($categorySummary as $category)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-ink-900/70">{{ $category->name }}</span>
                                <span class="font-medium text-ink-900">{{ $category->service_requests_count }}</span>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-rust rounded-full"
                                    style="width: {{ min(100, ($category->service_requests_count / max(1, $categorySummary->max('service_requests_count'))) * 100) }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-ink-900/40">{{ __('No categories yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <div class="xl:col-span-2 bg-white rounded-lg border border-ink-900/10 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-display font-semibold">{{ __('Income') }}</h2>
                    <form><select name="range" onchange="this.form.submit()" class="text-sm rounded border-ink-900/20">
                            <option value="day" @selected($range === 'day')>{{ __('Day') }}</option>
                            <option value="month" @selected($range === 'month')>{{ __('Month') }}</option>
                            <option value="year" @selected($range === 'year')>{{ __('Year') }}</option>
                        </select></form>
                </div><canvas id="wallet-chart" height="90"></canvas>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display font-semibold text-ink-900">{{ __('Income list') }}</h2>
                    <span class="text-xs text-ink-900/50">{{ __(ucfirst($range)) }}</span>
                </div>
                <div class="space-y-1 max-h-80 overflow-y-auto">
                    @forelse ($incomeTransactions as $row)
                        <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-ink-900">{{ __(ucwords(str_replace('_', ' ', $row->type))) }}</p>
                                <p class="text-[11px] text-ink-900/50">{{ $row->created_at->format('M j, g:i A') }} ·
                                    {{ __(ucwords(str_replace('_', ' ', $row->payment_method))) }}</p>
                            </div>
                            <span class="text-sm font-semibold text-moss">${{ number_format($row->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-ink-900/40">{{ __('No income recorded for this range.') }}</p>
                    @endforelse
                </div>
                @if ($incomeTransactions->isNotEmpty())
                    <div class="mt-3 pt-3 border-t border-ink-900/10 flex items-center justify-between">
                        <span class="text-xs text-ink-900/50">{{ __('Total') }}</span>
                        <span class="text-sm font-semibold text-ink-900">${{ number_format($incomeTransactions->sum('amount'), 2) }}</span>
                    </div>
                @endif
            </div>
        </div>
        @push('head')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endpush
        @push('head')
            <script>
                document.addEventListener('DOMContentLoaded', () => new Chart(document.getElementById('wallet-chart'), {
                    type: 'line',
                    data: {
                        labels: @json($chart['labels']),
                        datasets: [{
                            label: @json(__('Income')),
                            data: @json($chart['income']),
                            borderColor: '#3F7D58',
                            backgroundColor: 'transparent'
                        }]
                    }
                }))
            </script>
        @endpush
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold text-ink-900 mb-4">{{ __("Today's schedule") }}</h2>
            @forelse ($todaysSchedule as $sr)
                <a href="{{ route('service-requests.show', $sr) }}"
                    class="flex items-center justify-between py-2.5 border-b border-ink-900/5 last:border-0 hover:bg-paper -mx-2 px-2 rounded">
                    <div>
                        <p class="text-sm font-medium text-ink-900">{{ $sr->title }}</p>
                        <p class="text-xs text-ink-900/50">{{ $sr->customer->name }} ·
                            {{ $sr->technician->name ?? __('Unassigned') }}</p>
                    </div>
                    <span class="text-xs text-ink-900/50">{{ optional($sr->scheduled_at)->format('g:i A') }}</span>
                </a>
            @empty
                <p class="text-sm text-ink-900/40">{{ __('Nothing scheduled for today.') }}</p>
            @endforelse
        </div>

        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold text-ink-900 mb-4">{{ __('Recent services') }}</h2>
            @forelse ($recentTickets as $sr)
                <a href="{{ route('service-requests.show', $sr) }}"
                    class="flex items-center justify-between py-2.5 border-b border-ink-900/5 last:border-0 hover:bg-paper -mx-2 px-2 rounded">
                    <div>
                        <p class="text-sm font-medium text-ink-900">{{ $sr->title }}</p>
                        <p class="text-xs text-ink-900/50">{{ $sr->ticket_number }} · {{ $sr->customer->name }}</p>
                    </div>
                    <span
                        class="text-xs px-2 py-0.5 rounded-full {{ $sr->statusBadgeColor() }}">{{ __($sr->statusLabel()) }}</span>
                </a>
            @empty
                <p class="text-sm text-ink-900/40">{{ __('No services yet.') }}</p>
            @endforelse
        </div>
    </div>
@endsection
