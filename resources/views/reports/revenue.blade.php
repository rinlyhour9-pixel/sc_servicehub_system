@extends('layouts.app')
@section('title', __('Revenue Report'))
@section('content')
    @include('reports._filters')

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total income') }}</p>
            <p class="font-display text-2xl font-semibold text-moss">${{ number_format($income, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total expenses') }}</p>
            <p class="font-display text-2xl font-semibold text-rust">${{ number_format($expense, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Net') }}</p>
            <p class="font-display text-2xl font-semibold text-ink-900">${{ number_format($income - $expense, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Refunds') }}</p>
            <p class="font-display text-2xl font-semibold text-mustard">${{ number_format($refunds, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold mb-4">{{ __('Daily income vs expense') }}</h2>
            <canvas id="revenue-chart" height="100"></canvas>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-5">
            <h2 class="font-display font-semibold mb-4">{{ __('Income by payment method') }}</h2>
            <div class="flex items-center justify-between py-2 border-b border-ink-900/5">
                <span class="text-sm text-ink-900/60">{{ __('Cash') }}</span>
                <span class="text-sm font-semibold">${{ number_format($cash, 2) }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <span class="text-sm text-ink-900/60">{{ __('Bank / QR') }}</span>
                <span class="text-sm font-semibold">${{ number_format($bank, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden mt-6">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Date') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Income') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Expense') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Net') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daily as $row)
                    <tr class="border-t border-ink-900/5">
                        <td class="px-5 py-3">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-right text-moss">${{ number_format($row['income'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-rust">${{ number_format($row['expense'], 2) }}</td>
                        <td class="px-5 py-3 text-right font-medium">${{ number_format($row['income'] - $row['expense'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-6 text-center text-ink-900/40">{{ __('No transactions in this range.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => new Chart(document.getElementById('revenue-chart'), {
            type: 'bar',
            data: {
                labels: @json($daily->pluck('date')),
                datasets: [
                    { label: @json(__('Income')), data: @json($daily->pluck('income')), backgroundColor: '#3F7D58' },
                    { label: @json(__('Expense')), data: @json($daily->pluck('expense')), backgroundColor: '#BF5B2E' },
                ]
            },
            options: { responsive: true, scales: { x: { stacked: false }, y: { beginAtZero: true } } }
        }))
    </script>
@endpush
