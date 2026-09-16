@extends('layouts.app')
@section('title', __('Daily Cash Reconciliation'))
@section('content')
    @include('reports._filters')

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total cash difference') }}</p>
            <p class="font-display text-2xl font-semibold {{ $totalDiff < 0 ? 'text-rust' : ($totalDiff > 0 ? 'text-moss' : 'text-ink-900') }}">
                {{ $totalDiff >= 0 ? '+' : '' }}${{ number_format($totalDiff, 2) }}
            </p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Days with discrepancy') }}</p>
            <p class="font-display text-2xl font-semibold text-ink-900">{{ $daysWithDiscrepancy }} / {{ $days->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Business date') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Opening cash') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Expected') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Actual') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Difference') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($days as $day)
                    <tr class="border-t border-ink-900/5">
                        <td class="px-5 py-3">{{ $day->business_date->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-right">${{ number_format($day->opening_cash, 2) }}</td>
                        <td class="px-5 py-3 text-right">{{ $day->expected_cash !== null ? '$' . number_format($day->expected_cash, 2) : '—' }}</td>
                        <td class="px-5 py-3 text-right">{{ $day->actual_cash !== null ? '$' . number_format($day->actual_cash, 2) : '—' }}</td>
                        <td class="px-5 py-3 text-right font-semibold {{ $day->cash_difference < 0 ? 'text-rust' : ($day->cash_difference > 0 ? 'text-moss' : 'text-ink-900/60') }}">
                            {{ $day->cash_difference !== null ? ($day->cash_difference >= 0 ? '+' : '') . '$' . number_format($day->cash_difference, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $day->isOpen() ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                                {{ $day->isOpen() ? __('Open') : __('Closed') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-6 text-center text-ink-900/40">{{ __('No business days in this range.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
