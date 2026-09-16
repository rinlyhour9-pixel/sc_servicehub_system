@extends('layouts.app')
@section('title', $customer->name)
@section('header-actions')
    <div class="flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="border border-ink-900/20 text-ink-900 text-sm font-medium rounded-md px-4 py-2 hover:bg-white transition">{{ __('Edit') }}</a>
        <a href="{{ route('service-requests.create') }}" class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">+ {{ __('New ticket') }}</a>
    </div>
@endsection
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-ink-900/10 p-5 col-span-1 h-fit">
            <h2 class="font-display font-semibold text-ink-900 mb-3">{{ __('Contact') }}</h2>
            <dl class="text-sm space-y-2">
                <div><dt class="text-ink-900/50">{{ __('Email') }}</dt><dd class="text-ink-900">{{ $customer->email ?: '—' }}</dd></div>
                <div><dt class="text-ink-900/50">{{ __('Phone') }}</dt><dd class="text-ink-900">{{ $customer->phone ?: '—' }}</dd></div>
                <div><dt class="text-ink-900/50">{{ __('Address') }}</dt><dd class="text-ink-900">{{ $customer->address ?: '—' }}</dd></div>
                @if($customer->notes)
                    <div><dt class="text-ink-900/50">{{ __('Notes') }}</dt><dd class="text-ink-900">{{ $customer->notes }}</dd></div>
                @endif
            </dl>
            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="mt-5" onsubmit='return confirm(@json(__('Delete this customer and all their tickets?')))'>
                @csrf @method('DELETE')
                <button class="text-sm text-rust-600 hover:underline">{{ __('Delete customer') }}</button>
            </form>
        </div>

        <div class="col-span-2">
            <h2 class="font-display font-semibold text-ink-900 mb-3">{{ __('Service history') }}</h2>
            <div class="bg-white rounded-lg border border-ink-900/10 divide-y divide-ink-900/5">
                @forelse ($customer->serviceRequests as $sr)
                    <a href="{{ route('service-requests.show', $sr) }}" class="flex items-center justify-between px-5 py-3 hover:bg-paper">
                        <div>
                            <p class="text-sm font-medium text-ink-900">{{ $sr->title }}</p>
                            <p class="text-xs text-ink-900/50">{{ $sr->ticket_number }} · {{ $sr->category->name ?? __('Uncategorized') }} · {{ $sr->technician->name ?? __('Unassigned') }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $sr->statusBadgeColor() }}">{{ __($sr->statusLabel()) }}</span>
                    </a>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-ink-900/40">{{ __('No tickets yet for this customer.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
