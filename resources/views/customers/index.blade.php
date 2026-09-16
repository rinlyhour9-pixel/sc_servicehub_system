@extends('layouts.app')
@section('title', __('Customers'))
@section('header-actions')
    <a href="{{ route('customers.create') }}"
        class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">+ {{ __('New customer') }}</a>
@endsection
@section('content')
    <form method="GET" class="mb-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search customers...') }}"
            class="w-full max-w-sm rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
    </form>

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Name') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Contact') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Address') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Services') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t border-ink-900/5 hover:bg-paper cursor-pointer"
                        onclick="window.location='{{ route('customers.show', $customer) }}'">
                        <td class="px-5 py-3 font-medium text-ink-900">{{ $customer->name }}</td>
                        <td class="px-5 py-3 text-ink-900/60">{{ $customer->phone ?: __('No phone') }}</td>
                        <td class="px-5 py-3 text-ink-900/60">{{ $customer->address }}</td>
                        <td class="px-5 py-3 text-ink-900/60">{{ $customer->service_requests_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-6 text-center text-ink-900/40">{{ __('No customers found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
@endsection
