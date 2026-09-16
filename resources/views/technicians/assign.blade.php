@extends('layouts.app')
@section('title', __('Assign Technician'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6">
        <h2 class="font-display text-lg font-semibold mb-4">{{ __('Assign Technician') }}</h2>
        <p class="text-sm text-ink-900/60 mb-4">{{ __('Quickly assign technicians to open service requests.') }}</p>
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-paper p-4 rounded">
                <form method="POST" action="{{ route('technicians.assign.store') }}">
                    @csrf
                    <label class="block text-sm mb-1">{{ __('Technician') }}</label>
                    <select name="technician_id" class="w-full rounded border-ink-900/20 mb-3">
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}">{{ $tech->name }} — {{ $tech->phone }}</option>
                        @endforeach
                    </select>
                    <label class="block text-sm mb-1">{{ __('Service Request') }}</label>
                    <select name="request_id" class="w-full rounded border-ink-900/20 mb-3">
                        @foreach ($requests as $r)
                            <option value="{{ $r->id }}">{{ $r->ticket_number }} — {{ $r->title }}
                                ({{ $r->customer->name }})
                            </option>
                        @endforeach
                    </select>
                    <button class="bg-rust text-white px-3 py-2 rounded">{{ __('Assign') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
