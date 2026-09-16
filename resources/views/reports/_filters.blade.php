<form method="GET" class="flex flex-wrap items-end gap-3 mb-6 bg-white rounded-lg border border-ink-900/10 p-4">
    <div>
        <label class="block text-xs text-ink-900/50 mb-1">{{ __('From') }}</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}"
            class="rounded-md border-ink-900/20 text-sm">
    </div>
    <div>
        <label class="block text-xs text-ink-900/50 mb-1">{{ __('To') }}</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="rounded-md border-ink-900/20 text-sm">
    </div>
    <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2">{{ __('Apply') }}</button>
    <div class="ml-auto flex flex-wrap gap-2 text-xs">
        <a href="?from={{ now()->subDays(6)->toDateString() }}&to={{ now()->toDateString() }}"
            class="px-3 py-1.5 rounded-md border border-ink-900/15 hover:bg-paper">{{ __('7 days') }}</a>
        <a href="?from={{ now()->subDays(29)->toDateString() }}&to={{ now()->toDateString() }}"
            class="px-3 py-1.5 rounded-md border border-ink-900/15 hover:bg-paper">{{ __('30 days') }}</a>
        <a href="?from={{ now()->startOfMonth()->toDateString() }}&to={{ now()->toDateString() }}"
            class="px-3 py-1.5 rounded-md border border-ink-900/15 hover:bg-paper">{{ __('This month') }}</a>
        <a href="?from={{ now()->startOfYear()->toDateString() }}&to={{ now()->toDateString() }}"
            class="px-3 py-1.5 rounded-md border border-ink-900/15 hover:bg-paper">{{ __('This year') }}</a>
    </div>
</form>
