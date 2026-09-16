@extends('layouts.app')
@section('title', __('Technicians'))
@section('header-actions')<a href="{{ route('technicians.create') }}"
        class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2">
    + {{ __('Add technician') }}</a>@endsection
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 overflow-x-auto shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-paper text-left text-xs text-ink-900/60">
                <tr>
                    <th class="px-5 py-3">{{ __('Name') }}</th>
                    <th class="px-5 py-3">{{ __('Email / Phone') }}</th>
                    <th class="px-5 py-3">{{ __('Services') }}</th>
                    <th class="px-5 py-3">{{ __('Status') }}</th>
                    <th class="px-5 py-3">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @foreach ($users as $user)
                    <tr class="hover:bg-ink-900/5 transition-colors">
                        <td class="px-5 py-4 font-medium"><a class="hover:underline"
                                href="{{ route('technicians.edit', $user) }}">{{ $user->name }}</a></td>
                        <td class="px-5 py-4">{{ $user->email ?: '—' }}<p class="text-xs text-ink-900/45 mt-1">
                                {{ $user->phone ?: __('No phone') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if ($user->serviceCategories->isNotEmpty())
                                @foreach ($user->serviceCategories->take(3) as $c)
                                    <span
                                        class="inline-block bg-paper px-2 py-1 text-xs rounded mr-2">{{ $c->name }}</span>
                                @endforeach
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4"><span
                                class="text-xs font-medium {{ $user->is_active ? 'text-moss' : 'text-rust' }}">{{ $user->is_active ? __('Active') : __('Inactive') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('technicians.edit', $user) }}" class="text-xs px-2 py-1 rounded bg-paper mr-2">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('technicians.toggle-status', $user) }}" class="inline">@csrf
                                @method('PATCH')
                                <button
                                    class="text-xs px-2 py-1 rounded {{ $user->is_active ? 'bg-amber-50 text-rust' : 'bg-green-50 text-moss' }}">{{ $user->is_active ? __('Deactivate') : __('Activate') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
