@extends('layouts.app')
@section('title', __('Reports'))
@section('content')
    @php
        $reports = [
            ['route' => 'reports.revenue', 'title' => 'Revenue Report', 'desc' => 'Income by day, cash vs bank/QR, expenses and net total.', 'color' => 'text-moss'],
            ['route' => 'reports.service-requests', 'title' => 'Service Requests Report', 'desc' => 'Ticket volume by status, priority, category, and average turnaround.', 'color' => 'text-rust'],
            ['route' => 'reports.technicians', 'title' => 'Technician Performance', 'desc' => 'Jobs completed vs open per technician, with completion rate.', 'color' => 'text-mustard'],
            ['route' => 'reports.customers', 'title' => 'Customer Report', 'desc' => 'Top customers by revenue and service history.', 'color' => 'text-ink-800'],
            ['route' => 'reports.invoices', 'title' => 'Invoices & Billing', 'desc' => 'Paid vs unpaid totals and outstanding balance aging.', 'color' => 'text-moss'],
            ['route' => 'reports.cash', 'title' => 'Daily Cash Reconciliation', 'desc' => 'Expected vs actual cash per closed business day.', 'color' => 'text-rust'],
        ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($reports as $r)
            <a href="{{ route($r['route']) }}"
                class="bg-white rounded-lg border border-ink-900/10 p-5 hover:shadow-md hover:border-rust/30 transition block">
                <h2 class="font-display font-semibold {{ $r['color'] }} mb-1">{{ __($r['title']) }}</h2>
                <p class="text-sm text-ink-900/60">{{ __($r['desc']) }}</p>
            </a>
        @endforeach
    </div>
@endsection
