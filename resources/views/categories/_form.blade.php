<div>
    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Name') }}</label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
           class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
</div>
<div>
    <label class="block text-sm font-medium text-ink-900 mb-1">{{ __('Description') }}</label>
    <textarea name="description" rows="2" class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">{{ old('description', $category->description ?? '') }}</textarea>
</div>
