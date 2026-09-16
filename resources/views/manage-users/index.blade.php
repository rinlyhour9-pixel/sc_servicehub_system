@extends('layouts.app')
@section('title', __('Manage Users'))
@section('content')
    <div class="flex gap-2 mb-6 border-b border-ink-900/10">
        @foreach (['administrator' => __('Administrators'), 'technician' => __('Technicians'), 'client' => __('Clients')] as $key => $label)
            <a href="{{ route('manage-users.index', ['tab' => $key]) }}"
                class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition
                    {{ $tab === $key ? 'border-rust text-rust' : 'border-transparent text-ink-900/50 hover:text-ink-900' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @php
        $rows = match ($tab) {
            'technician' => $technicians,
            'client' => $clients,
            default => $administrators,
        };
    @endphp

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Name') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Phone') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Status') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Login') }}</th>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $account)
                    <tr class="border-t border-ink-900/5">
                        <td class="px-5 py-3 font-medium text-ink-900">{{ $account->name }}</td>
                        <td class="px-5 py-3 text-ink-900/70">{{ $account->phone ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs {{ $account->is_active ? 'text-moss' : 'text-rust' }}">{{ $account->is_active ? __('Active') : __('Inactive') }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs text-ink-900/50">
                            {{ ! empty($account->password) ? __('Enabled') : __('Not set') }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if ($tab === 'administrator')
                                    <a href="{{ route('users.edit', $account) }}" class="text-xs px-2 py-1 rounded bg-paper">{{ __('Edit') }}</a>
                                    @if ($account->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle-status', $account) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs px-2 py-1 rounded {{ $account->is_active ? 'bg-amber-50 text-rust' : 'bg-green-50 text-moss' }}">{{ $account->is_active ? __('Deactivate') : __('Activate') }}</button>
                                        </form>
                                    @endif
                                @elseif ($tab === 'technician')
                                    <a href="{{ route('technicians.edit', $account) }}" class="text-xs px-2 py-1 rounded bg-paper">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('technicians.toggle-status', $account) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded {{ $account->is_active ? 'bg-amber-50 text-rust' : 'bg-green-50 text-moss' }}">{{ $account->is_active ? __('Deactivate') : __('Activate') }}</button>
                                    </form>
                                @else
                                    <a href="{{ route('customers.edit', $account) }}" class="text-xs px-2 py-1 rounded bg-paper">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('customers.toggle-status', $account) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded {{ $account->is_active ? 'bg-amber-50 text-rust' : 'bg-green-50 text-moss' }}">{{ $account->is_active ? __('Deactivate') : __('Activate') }}</button>
                                    </form>
                                @endif

                                <details class="relative">
                                    <summary class="text-xs px-2 py-1 rounded bg-ink-900/5 cursor-pointer list-none">{{ __('Set password') }}</summary>
                                    <form method="POST" action="{{ route('manage-users.reset-password', ['role' => $tab, 'id' => $account->id]) }}"
                                        class="absolute z-10 right-0 mt-1 w-56 bg-white border border-ink-900/10 rounded-md shadow-lg p-3 space-y-2">
                                        @csrf
                                        <input type="password" name="password" placeholder="{{ __('New password') }}" required
                                            class="w-full text-xs rounded border-ink-900/20">
                                        <input type="password" name="password_confirmation" placeholder="{{ __('Confirm password') }}" required
                                            class="w-full text-xs rounded border-ink-900/20">
                                        <button class="w-full bg-rust text-white text-xs rounded px-2 py-1.5">{{ __('Save password') }}</button>
                                    </form>
                                </details>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-ink-900/40">{{ __('No accounts found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $rows->links() }}</div>
@endsection
