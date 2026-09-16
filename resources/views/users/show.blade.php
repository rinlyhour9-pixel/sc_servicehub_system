@extends('layouts.app')
@section('title', __('User Profile'))
@section('content')
    <div class="max-w-2xl bg-white rounded-lg border border-ink-900/10 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-display text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-ink-900/60">{{ __('Administrator') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm {{ $user->is_active ? 'text-moss' : 'text-rust' }}">
                    {{ $user->is_active ? __('Active') : __('Inactive') }}</p>
                <div class="mt-2 flex items-center justify-end space-x-2">
                    <a href="{{ route('users.edit', $user) }}" class="text-xs bg-paper px-3 py-1 rounded">{{ __('Edit') }}</a>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="p-2 bg-white rounded border border-ink-900/10 shadow-sm">
                            <svg class="h-4 w-4 text-ink-900" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path d="M3 12h18M3 6h18M3 18h18" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                            class="absolute right-0 mt-2 w-44 bg-white border border-ink-900/10 rounded shadow z-50">
                            <a href="{{ route('users.edit', $user) }}"
                                class="block px-3 py-2 text-sm hover:bg-paper">{{ __('Edit') }}</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}">@csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-paper">{{ $user->is_active ? __('Deactivate') : __('Activate') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-xs text-ink-900/60">{{ __('Email') }}</div>
                <div class="font-medium">{{ $user->email }}</div>
            </div>
            <div>
                <div class="text-xs text-ink-900/60">{{ __('Phone') }}</div>
                <div class="font-medium">{{ $user->phone ?: __('No phone') }}</div>
            </div>
        </div>
    </div>
@endsection
