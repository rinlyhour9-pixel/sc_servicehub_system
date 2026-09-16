@extends('layouts.app')
@section('title', __('Technician Performance'))
@section('content')
    @include('reports._filters')

    <div class="bg-white rounded-lg border border-ink-900/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink-900/60 text-xs">
                <tr>
                    <th class="text-left px-5 py-3 font-medium">{{ __('Technician') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Total jobs') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Completed') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Open') }}</th>
                    <th class="text-right px-5 py-3 font-medium">{{ __('Completion rate') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($technicians as $tech)
                    <tr class="border-t border-ink-900/5">
                        <td class="px-5 py-3">
                            <p class="font-medium text-ink-900">{{ $tech->name }}</p>
                            <p class="text-xs text-ink-900/40">{{ $tech->is_active ? __('Active') : __('Inactive') }}</p>
                        </td>
                        <td class="px-5 py-3 text-right">{{ $tech->total_jobs }}</td>
                        <td class="px-5 py-3 text-right text-moss">{{ $tech->completed_jobs }}</td>
                        <td class="px-5 py-3 text-right text-rust">{{ $tech->open_jobs }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="inline-block bg-paper rounded-full px-2 py-0.5 text-xs font-semibold">{{ $tech->completion_rate }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-ink-900/40">{{ __('No technicians found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
