@extends('layouts.app')
@section('title', __('Edit ticket'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-2xl">
        <form method="POST" action="{{ route('service-requests.update', $serviceRequest) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Customer') }}</label>
                    <select name="customer_id" required class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id', $serviceRequest->customer_id) == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Category') }}</label>
                    <select name="service_category_id" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <option value="">{{ __('Uncategorized') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('service_category_id', $serviceRequest->service_category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Title') }}</label>
                <input type="text" name="title" value="{{ old('title', $serviceRequest->title) }}" required
                       class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Description') }}</label>
                <textarea name="description" rows="4" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">{{ old('description', $serviceRequest->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Service address') }}</label>
                <input type="text" name="service_address" value="{{ old('service_address', $serviceRequest->service_address) }}"
                       class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Priority') }}</label>
                    <select name="priority" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        @foreach(\App\Models\ServiceRequest::PRIORITIES as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $serviceRequest->priority) === $priority)>{{ __(ucfirst($priority)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Scheduled') }}</label>
                    <input type="datetime-local" name="scheduled_at"
                           value="{{ old('scheduled_at', optional($serviceRequest->scheduled_at)->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                </div>
            </div>
            <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Save changes') }}</button>
        </form>
    </div>
@endsection
