@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('header-actions')
    <div class="flex gap-2">
        <a href="{{ route('invoices.edit', $invoice) }}" class="border border-ink-900/20 text-ink-900 text-sm font-medium rounded-md px-4 py-2 hover:bg-white transition">{{ __('Edit') }}</a>
        <a href="{{ route('service-requests.show', $invoice->serviceRequest) }}" class="border border-ink-900/20 text-ink-900 text-sm font-medium rounded-md px-4 py-2 hover:bg-white transition">{{ __('View ticket') }}</a>
    </div>
@endsection
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-2xl">
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="font-display text-lg font-semibold text-ink-900">{{ $invoice->invoice_number }}</p>
                <p class="text-sm text-ink-900/60">{{ $invoice->serviceRequest->customer->name }} · {{ $invoice->serviceRequest->title }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full
                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                {{ __(ucfirst($invoice->status)) }}
            </span>
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="text-ink-900/50 text-xs border-b border-ink-900/10">
                <tr>
                    <th class="text-left py-2 font-medium">{{ __('Description') }}</th>
                    <th class="text-right py-2 font-medium">{{ __('Qty') }}</th>
                    <th class="text-right py-2 font-medium">{{ __('Unit price') }}</th>
                    <th class="text-right py-2 font-medium">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="border-b border-ink-900/5">
                        <td class="py-2 text-ink-900">{{ $item->description }}</td>
                        <td class="py-2 text-right text-ink-900/70">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                        <td class="py-2 text-right text-ink-900/70">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2 text-right text-ink-900 font-medium">${{ number_format($item->total(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="text-right">
                <p class="text-xs text-ink-900/50">{{ __('Total') }}</p>
                <p class="font-display text-2xl font-semibold text-ink-900">${{ number_format($invoice->total(), 2) }}</p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-ink-900/5 text-xs text-ink-900/50 flex justify-between">
            <span>{{ __('Issued') }} {{ optional($invoice->issued_at)->format('M j, Y') }}</span>
            <span>
                @if ($invoice->walletTransaction)
                    {{ __('Paid') }} {{ $invoice->paid_at->format('M j, Y') }} · {{ __(ucwords(str_replace('_', ' ', $invoice->walletTransaction->payment_method))) }} · {{ __('recorded in cash drawer') }}
                @elseif ($invoice->paid_at)
                    {{ __('Paid') }} {{ $invoice->paid_at->format('M j, Y') }}
                @else
                    {{ __('Not yet paid') }}
                @endif
            </span>
        </div>

        @if ($invoice->status === \App\Models\Invoice::STATUS_UNPAID)
            <div class="mt-4 pt-4 border-t border-ink-900/5">
                <form method="POST" action="{{ route('invoices.record-payment', $invoice) }}" class="flex items-end gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-ink-900/60 mb-1">{{ __('Record payment via') }}</label>
                        <select name="payment_method" class="rounded-md border-ink-900/20 text-sm">
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="bank_qr">{{ __('Bank / QR') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>
                    <button class="bg-moss hover:bg-moss/90 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Mark as paid') }}</button>
                </form>
                <p class="text-xs text-ink-900/40 mt-2">{{ __("This records the payment in today's cash drawer under Wallet & Cash.") }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="mt-4" onsubmit="return confirm(@json(__('Delete this invoice?')))">
            @csrf @method('DELETE')
            <button class="text-sm text-rust-600 hover:underline">{{ __('Delete invoice') }}</button>
        </form>
    </div>
@endsection
