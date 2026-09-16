@extends('layouts.app')
@section('title', __('Edit category'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-xl">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('categories._form', ['category' => $category])
            <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Save changes') }}</button>
        </form>
    </div>
@endsection
