@extends('layouts.app')
@section('title', __('Invoices & Billing Report'))
@section('content')
    @include('reports._filters')

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total invoiced') }}</p>
            <p class="font-display text-2xl font-semibold text-ink-900">${{ number_format($totalInvoiced, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Total paid') }}</p>
            <p class="font-display text-2xl font-semibold text-moss">${{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-ink-900/10 p-4">
            <p class="text-xs text-ink-900/50">{{ __('Outstanding') }}</p>
            <p class="font-display text-2xl font-semibold text-rust">${{ number_format($totalOutstanding, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-ink-900/10 p-5 mb-6">
        <h2 class="font-display font-semibold mb-4">{{ __('Outstanding balance aging') }}</h2>
        <div class="grid grid-cols-3 gap-4">
            @foreach ($aging as $bucket => $amount)
                <div>
                    <p class="text-xs text-ink-900/50">{{ __($bucket) }}</p>
                    <p class="font-display text-xl font-semibold {{ $bucket === '30+ days' ? 'text-rust' : 'text-ink-900' }}">${{ number_format($amount, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Invoice') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Customer') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Issued') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Amount') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-t border-ink-900/5 hover:bg-paper cursor-pointer"
                        onclick="window.location='{{ route('invoices.show', $invoice) }}'">
                        <td class="px-5 py-3 font-mono text-xs text-ink-900/70">{{ $invoice->invoice_number }}</td>
                        <td class="px-5 py-3">{{ $invoice->serviceRequest->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-ink-900/60">{{ $invoice->issued_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-right font-semibold">${{ number_format($invoice->total(), 2) }}</td>
                        <td class="px-5 py-3">
                            <span
                                class="text-xs px-2 py-0.5 rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">{{ __(ucfirst($invoice->status)) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-ink-900/40">{{ __('No invoices in this range.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
