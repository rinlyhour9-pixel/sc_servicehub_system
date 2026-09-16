@extends('layouts.app')
@section('title', __('Invoices'))
@section('header-actions')
    <a href="{{ route('invoices.create') }}" class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">+ {{ __('New invoice') }}</a>
@endsection
@section('content')
    <form method="GET" class="mb-5">
        <select name="status" onchange="this.form.submit()" class="rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            <option value="">{{ __('All statuses') }}</option>
            @foreach(\App\Models\Invoice::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst($status)) }}</option>
            @endforeach
        </select>
    </form>
    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Invoice') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Customer') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Ticket') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Total') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Status') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Issued') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-t border-ink-900/5 hover:bg-paper cursor-pointer" onclick="window.location='{{ route('invoices.show', $invoice) }}'">
                        <td class="px-5 py-3 font-mono text-xs text-ink-900/70">{{ $invoice->invoice_number }}</td>
                        <td class="px-5 py-3 text-ink-900">{{ $invoice->serviceRequest->customer->name }}</td>
                        <td class="px-5 py-3 text-ink-900/60">{{ $invoice->serviceRequest->title }}</td>
                        <td class="px-5 py-3 font-medium text-ink-900">${{ number_format($invoice->total(), 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ __(ucfirst($invoice->status)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-ink-900/60">{{ optional($invoice->issued_at)->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-6 text-center text-ink-900/40">{{ __('No invoices found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
@endsection
