@extends('layouts.app')
@section('title', __('Customer Report'))
@section('content')
    @include('reports._filters')

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Customer') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Requests') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Completed') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Revenue') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Last service') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t border-ink-900/5 hover:bg-paper cursor-pointer"
                        onclick="window.location='{{ route('customers.show', $customer) }}'">
                        <td class="px-5 py-3">
                            <p class="font-medium text-ink-900">{{ $customer->name }}</p>
                            <p class="text-xs text-ink-900/40">{{ $customer->phone ?: $customer->email }}</p>
                        </td>
                        <td class="px-5 py-3 text-right">{{ $customer->request_count }}</td>
                        <td class="px-5 py-3 text-right text-moss">{{ $customer->completed_count }}</td>
                        <td class="px-5 py-3 text-right font-semibold">${{ number_format($customer->revenue, 2) }}</td>
                        <td class="px-5 py-3 text-ink-900/60">
                            {{ $customer->last_service_at ? \Illuminate\Support\Carbon::parse($customer->last_service_at)->format('M j, Y') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-ink-900/40">{{ __('No customer activity in this range.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
