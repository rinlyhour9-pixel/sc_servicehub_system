@extends('layouts.app')
@section('title', __('New invoice'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-2xl">
        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Service ticket') }}</label>
                <select name="service_request_id" required class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    <option value="">{{ __('Select a ticket without an invoice') }}</option>
                    @foreach($serviceRequests as $sr)
                        <option value="{{ $sr->id }}" @selected($selectedServiceRequestId == $sr->id)>{{ $sr->ticket_number }} — {{ $sr->title }} ({{ $sr->customer->name }})</option>
                    @endforeach
                </select>
            </div>

            <div id="items">
                <div class="grid grid-cols-12 gap-2 text-xs text-ink-900/50 mb-1 px-1">
                    <div class="col-span-6">{{ __('Description') }}</div>
                    <div class="col-span-2">{{ __('Qty') }}</div>
                    <div class="col-span-3">{{ __('Unit price') }}</div>
                </div>
                @for ($i = 0; $i < 3; $i++)
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <input type="text" name="items[{{ $i }}][description]" placeholder="{{ __('Labor, parts, etc.') }}" class="col-span-6 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="1" class="col-span-2 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="0" class="col-span-3 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    </div>
                @endfor
            </div>
            <p class="text-xs text-ink-900/40 mb-4">{{ __("Leave a row's description blank to skip it. Add more rows after saving if needed.") }}</p>

            <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Create invoice') }}</button>
        </form>
    </div>
@endsection
