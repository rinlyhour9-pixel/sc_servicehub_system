@extends('layouts.app')
@section('title', __('Edit invoice') . ' ' . $invoice->invoice_number)
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-2xl">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Status') }}</label>
                @if ($invoice->status === \App\Models\Invoice::STATUS_PAID)
                    <input type="hidden" name="status" value="paid">
                    <p class="text-sm text-ink-900/60">{{ __("Paid — status is locked since it's already recorded in the cash drawer.") }}</p>
                @else
                    <select name="status" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <option value="unpaid" @selected($invoice->status === 'unpaid')>{{ __('Unpaid') }}</option>
                        <option value="cancelled" @selected($invoice->status === 'cancelled')>{{ __('Cancelled') }}</option>
                    </select>
                    <p class="text-xs text-ink-900/40 mt-1">{{ __('To mark this invoice paid, use "Record payment" on the invoice page — it logs the payment in the cash drawer too.') }}</p>
                @endif
            </div>

            <div class="grid grid-cols-12 gap-2 text-xs text-ink-900/50 mb-1 px-1">
                <div class="col-span-6">{{ __('Description') }}</div>
                <div class="col-span-2">{{ __('Qty') }}</div>
                <div class="col-span-3">{{ __('Unit price') }}</div>
            </div>
            @foreach($invoice->items as $i => $item)
                <div class="grid grid-cols-12 gap-2 mb-2">
                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                    <input type="text" name="items[{{ $i }}][description]" value="{{ $item->description }}" class="col-span-6 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    <input type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" class="col-span-2 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" class="col-span-3 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                </div>
            @endforeach
            @for ($i = count($invoice->items); $i < count($invoice->items) + 2; $i++)
                <div class="grid grid-cols-12 gap-2 mb-2">
                    <input type="text" name="items[{{ $i }}][description]" placeholder="{{ __('Add another item') }}" class="col-span-6 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    <input type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="1" class="col-span-2 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="0" class="col-span-3 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                </div>
            @endfor
            <p class="text-xs text-ink-900/40 mb-4">{{ __('Leave a row\'s description blank to remove it from the invoice.') }}</p>

            <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Save invoice') }}</button>
        </form>
    </div>
@endsection
