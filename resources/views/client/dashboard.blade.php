@extends('layouts.portal')
@section('title', __('My Services'))
@section('portal-name', __('Customer Portal'))
@section('logout-route', route('client.logout'))
@php($portalUser = $customer)
@section('content')
    <h2 class="font-display font-semibold text-ink-900 mb-3">{{ __('My service requests') }}</h2>
    <div class="space-y-3">
        @forelse ($serviceRequests as $sr)
            <div class="bg-white rounded-lg border border-ink-900/10 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium text-ink-900">{{ $sr->title }}</p>
                        <p class="text-xs text-ink-900/40 font-mono">{{ $sr->ticket_number }}</p>
                        <p class="text-sm text-ink-900/60 mt-1">{{ $sr->category->name ?? __('General') }} ·
                            {{ __('Technician') }}: {{ $sr->technician->name ?? __('Not yet assigned') }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $sr->statusBadgeColor() }}">{{ __($sr->statusLabel()) }}</span>
                </div>

                @if ($sr->invoice)
                    <div class="mt-3 pt-3 border-t border-ink-900/5 flex items-center justify-between">
                        <span class="text-sm text-ink-900/60">{{ __('Invoice') }} {{ $sr->invoice->invoice_number }}</span>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm">${{ number_format($sr->invoice->total(), 2) }}</span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full {{ $sr->invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ __(ucfirst($sr->invoice->status)) }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-ink-900/40">{{ __('You have no service requests yet.') }}</p>
        @endforelse
    </div>
@endsection
