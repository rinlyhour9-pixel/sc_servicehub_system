@extends('layouts.app')
@section('title', __('New customer'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-xl">
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
            @csrf
            @include('customers._form')
            <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Create customer') }}</button>
        </form>
    </div>
@endsection
