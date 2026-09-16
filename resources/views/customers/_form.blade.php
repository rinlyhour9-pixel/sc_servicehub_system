<div>
    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Name') }}</label>
    <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
           class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Email') }}</label>
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
               class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Phone') }}</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
               class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Address') }}</label>
    <input type="text" name="address" value="{{ old('address', $customer->address ?? '') }}"
           class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
</div>
<div>
    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Notes') }}</label>
    <textarea name="notes" rows="3" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">{{ old('notes', $customer->notes ?? '') }}</textarea>
</div>
