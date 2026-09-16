@extends('layouts.app')
@section('title', __('Service Pipeline'))
@section('header-actions')
    <a href="{{ route('service-requests.index') }}"
        class="border border-ink-900/20 text-ink-900 text-sm font-medium rounded-md px-4 py-2 hover:bg-white">{{ __('List view') }}</a>
    <a href="{{ route('service-requests.create') }}"
        class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">+ {{ __('New service') }}</a>
@endsection
@section('content')
    <p class="text-sm text-ink-900/50 mb-4">{{ __('Drag a ticket into another column to update its status.') }}</p>

    <div class="flex gap-4 overflow-x-auto pb-4" style="scrollbar-gutter: stable;">
        @foreach ($columns as $status)
            @php $rows = $serviceRequests->get($status, collect()) @endphp
            <div class="board-column shrink-0 w-72 bg-paper rounded-lg border border-ink-900/10 flex flex-col"
                data-status="{{ $status }}">
                <div class="px-3 py-3 border-b border-ink-900/10 flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wide text-ink-900/60">
                        {{ __(ucwords(str_replace('_', ' ', $status))) }}
                    </span>
                    <span class="text-xs font-semibold bg-white rounded-full px-2 py-0.5 text-ink-900/60">{{ $rows->count() }}</span>
                </div>
                <div class="board-dropzone flex-1 p-2 space-y-2 min-h-[120px]" data-status="{{ $status }}">
                    @foreach ($rows as $sr)
                        <a href="{{ route('service-requests.show', $sr) }}" draggable="true"
                            data-id="{{ $sr->id }}"
                            class="board-card block bg-white rounded-lg border border-ink-900/10 p-3 shadow-sm hover:shadow-md transition cursor-grab active:cursor-grabbing">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-ink-900 leading-snug">{{ $sr->title }}</p>
                                <span
                                    class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-full {{ $sr->priorityBadgeColor() }}">{{ __(ucfirst($sr->priority)) }}</span>
                            </div>
                            <p class="mt-1 text-xs text-ink-900/40 font-mono">{{ $sr->ticket_number }}</p>
                            <p class="mt-2 text-xs text-ink-900/60">{{ $sr->customer->name }}</p>
                            <p class="text-xs text-ink-900/40">{{ $sr->technician->name ?? __('Unassigned') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('head')
    <style>
        .board-dropzone.drag-over {
            background-color: rgba(191, 91, 46, 0.08);
            outline: 2px dashed rgba(191, 91, 46, 0.4);
            outline-offset: -4px;
            border-radius: 0.5rem;
        }

        .board-card.dragging {
            opacity: 0.4;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            let draggedId = null;

            document.querySelectorAll('.board-card').forEach(card => {
                card.addEventListener('dragstart', (e) => {
                    draggedId = card.dataset.id;
                    card.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });
                card.addEventListener('dragend', () => card.classList.remove('dragging'));
            });

            document.querySelectorAll('.board-dropzone').forEach(zone => {
                zone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    zone.classList.add('drag-over');
                });
                zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
                zone.addEventListener('drop', async (e) => {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                    if (!draggedId) return;

                    const status = zone.dataset.status;
                    try {
                        const res = await fetch(`/service-requests/${draggedId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ status }),
                        });
                        if (res.ok || res.redirected) {
                            window.location.reload();
                        }
                    } catch (err) {
                        console.error('Failed to update status', err);
                    }
                    draggedId = null;
                });
            });
        });
    </script>
@endpush
