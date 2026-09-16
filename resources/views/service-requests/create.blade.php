@extends('layouts.app')
@section('title', __('New ticket'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6 max-w-2xl">
        <form method="POST" action="{{ route('service-requests.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div x-data="{ newCustomer: {{ old('new_customer_name') ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-ink-900">{{ __('Customer') }}</label>
                        <button type="button" @click="newCustomer = !newCustomer" class="text-xs font-medium text-rust hover:underline">
                            <span x-show="!newCustomer">{{ __('+ New customer') }}</span>
                            <span x-show="newCustomer" x-cloak>{{ __('Choose existing') }}</span>
                        </button>
                    </div>

                    <div x-show="!newCustomer">
                        <select name="customer_id" :required="!newCustomer" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                            <option value="">{{ __('Select customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}{{ $customer->phone ? ' — '.$customer->phone : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="newCustomer" x-cloak class="grid grid-cols-2 gap-2">
                        <input type="text" name="new_customer_name" value="{{ old('new_customer_name') }}" :required="newCustomer"
                               placeholder="{{ __('Customer name') }}"
                               class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <input type="text" name="new_customer_phone" value="{{ old('new_customer_phone') }}"
                               placeholder="{{ __('Phone number') }}"
                               class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Category') }}</label>
                    <select name="service_category_id" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <option value="">{{ __('Uncategorized') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('service_category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Title') }}</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="{{ __('e.g. Kitchen sink leaking') }}"
                       class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Description') }}</label>
                <textarea name="description" rows="4" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Service address') }}</label>
                <input type="text" name="service_address" value="{{ old('service_address') }}" placeholder="{{ __('Leave blank to use customer address') }}"
                       class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Priority') }}</label>
                    <select name="priority" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        @foreach(\App\Models\ServiceRequest::PRIORITIES as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>{{ __(ucfirst($priority)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Technician') }}</label>
                    <select name="assigned_technician_id" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected(old('assigned_technician_id') == $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Scheduled') }}</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                           class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                </div>
            </div>

            <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Create ticket') }}</button>
        </form>
    </div>
@endsection
