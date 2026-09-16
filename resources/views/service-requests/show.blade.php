@extends('layouts.app')
@section('title', $serviceRequest->ticket_number)
@section('header-actions')
    <div class="flex gap-2">
        <a href="{{ route('service-requests.edit', $serviceRequest) }}" class="border border-ink-900/20 text-ink-900 text-sm font-medium rounded-md px-4 py-2 hover:bg-white transition">{{ __('Edit') }}</a>
        @if(!$serviceRequest->invoice)
            <a href="{{ route('invoices.create', ['service_request_id' => $serviceRequest->id]) }}" class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Create invoice') }}</a>
        @else
            <a href="{{ route('invoices.show', $serviceRequest->invoice) }}" class="bg-ink-800 hover:bg-ink-700 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('View invoice') }}</a>
        @endif
    </div>
@endsection
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-6">
            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-mono text-ink-900/40">{{ $serviceRequest->ticket_number }}</p>
                    <div class="flex gap-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $serviceRequest->priorityBadgeColor() }}">{{ __(ucfirst($serviceRequest->priority)) }} {{ __('priority') }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $serviceRequest->statusBadgeColor() }}">{{ __($serviceRequest->statusLabel()) }}</span>
                    </div>
                </div>
                <h2 class="font-display text-lg font-semibold text-ink-900 mb-2">{{ $serviceRequest->title }}</h2>
                <p class="text-sm text-ink-900/70 whitespace-pre-line">{{ $serviceRequest->description ?: __('No description provided.') }}</p>

                <dl class="grid grid-cols-2 gap-4 mt-5 pt-5 border-t border-ink-900/5 text-sm">
                    <div><dt class="text-ink-900/50 text-xs mb-0.5">{{ __('Customer') }}</dt><dd><a href="{{ route('customers.show', $serviceRequest->customer) }}" class="text-rust-600 font-medium">{{ $serviceRequest->customer->name }}</a></dd></div>
                    <div><dt class="text-ink-900/50 text-xs mb-0.5">{{ __('Category') }}</dt><dd class="text-ink-900">{{ $serviceRequest->category->name ?? '—' }}</dd></div>
                    <div><dt class="text-ink-900/50 text-xs mb-0.5">{{ __('Service address') }}</dt><dd class="text-ink-900">{{ $serviceRequest->service_address ?: '—' }}</dd></div>
                    <div><dt class="text-ink-900/50 text-xs mb-0.5">{{ __('Scheduled') }}</dt><dd class="text-ink-900">{{ optional($serviceRequest->scheduled_at)->format('M j, Y g:i A') ?? '—' }}</dd></div>
                    <div><dt class="text-ink-900/50 text-xs mb-0.5">{{ __('Created by') }}</dt><dd class="text-ink-900">{{ $serviceRequest->creator->name ?? '—' }}</dd></div>
                    <div><dt class="text-ink-900/50 text-xs mb-0.5">{{ __('Completed') }}</dt><dd class="text-ink-900">{{ optional($serviceRequest->completed_at)->format('M j, Y g:i A') ?? '—' }}</dd></div>
                </dl>
            </div>

            @php
                $trackingSteps = [
                    __('Booking confirmed') => \App\Models\ServiceRequest::STATUS_PENDING,
                    __('Technician assigned') => \App\Models\ServiceRequest::STATUS_ASSIGNED,
                    __('Service in progress') => \App\Models\ServiceRequest::STATUS_IN_PROGRESS,
                    __('Service complete') => \App\Models\ServiceRequest::STATUS_COMPLETED,
                ];
                $stepOrder = array_values($trackingSteps);
                $currentStep = array_search($serviceRequest->status, $stepOrder, true);
                $currentStep = $currentStep === false ? 0 : $currentStep;
            @endphp
            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h3 class="font-display font-semibold text-ink-900 mb-5">{{ __('Service tracking') }}</h3>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($trackingSteps as $label => $status)
                        @php($done = array_search($status, $stepOrder, true) <= $currentStep)
                        <div class="relative {{ $done ? 'text-moss' : 'text-ink-900/35' }}">
                            <div class="h-8 w-8 rounded-full grid place-items-center text-xs font-semibold {{ $done ? 'bg-moss text-white' : 'bg-ink-900/10' }}">{{ $loop->iteration }}</div>
                            @if(!$loop->last)<div class="absolute h-0.5 left-8 right-0 top-4 {{ $done && $loop->iteration <= $currentStep ? 'bg-moss' : 'bg-ink-900/10' }}"></div>@endif
                            <p class="mt-2 text-xs font-medium leading-tight">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h3 class="font-display font-semibold text-ink-900 mb-4">{{ __('Activity & notes') }}</h3>
                <form method="POST" action="{{ route('service-requests.notes.store', $serviceRequest) }}" class="mb-5">
                    @csrf
                    <textarea name="body" rows="2" required placeholder="{{ __('Add a note about this ticket...') }}"
                              class="w-full rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm mb-2"></textarea>
                    <button class="bg-ink-800 hover:bg-ink-700 text-white text-sm font-medium rounded-md px-4 py-2 transition">{{ __('Add note') }}</button>
                </form>
                <div class="space-y-4">
                    @forelse ($serviceRequest->notes as $note)
                        <div class="border-l-2 border-rust/30 pl-3">
                            <p class="text-sm text-ink-900">{{ $note->body }}</p>
                            <p class="text-xs text-ink-900/40 mt-0.5">{{ $note->authorName() }} · {{ $note->created_at->format('M j, g:i A') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-ink-900/40">{{ __('No notes yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h3 class="font-display font-semibold text-ink-900 mb-4">{{ __('Attachments') }}</h3>
                <form method="POST" action="{{ route('service-requests.attachments.store', $serviceRequest) }}" enctype="multipart/form-data" class="flex gap-2 mb-4">
                    @csrf
                    <input type="file" name="file" required class="flex-1 text-sm">
                    <button class="border border-ink-900/20 text-ink-900 text-sm rounded-md px-4 py-2 hover:bg-paper">{{ __('Upload') }}</button>
                </form>
                <ul class="space-y-2">
                    @forelse ($serviceRequest->attachments as $attachment)
                        <li class="flex items-center justify-between text-sm">
                            <a href="{{ $attachment->url() }}" target="_blank" class="text-rust-600 hover:underline">{{ $attachment->original_name }}</a>
                            <div class="flex items-center gap-3 text-xs text-ink-900/40">
                                <span>{{ $attachment->uploader->name ?? '—' }}</span>
                                <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm(@json(__('Remove this file?')))">
                                    @csrf @method('DELETE')
                                    <button class="text-rust-600 hover:underline">{{ __('Remove') }}</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <p class="text-sm text-ink-900/40">{{ __('No files attached.') }}</p>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h3 class="font-display font-semibold text-ink-900 mb-3 text-sm">{{ __('Update status') }}</h3>
                <form method="POST" action="{{ route('service-requests.update-status', $serviceRequest) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="flex-1 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        @foreach(\App\Models\ServiceRequest::STATUSES as $status)
                            <option value="{{ $status }}" @selected($serviceRequest->status === $status)>{{ __(ucwords(str_replace('_',' ',$status))) }}</option>
                        @endforeach
                    </select>
                    <button class="bg-rust hover:bg-rust-600 text-white text-sm font-medium rounded-md px-3 py-2 transition">{{ __('Save') }}</button>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <h3 class="font-display font-semibold text-ink-900 mb-3 text-sm">{{ __('Technician') }}</h3>
                <form method="POST" action="{{ route('service-requests.assign', $serviceRequest) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="assigned_technician_id" class="flex-1 rounded-md border-ink-900/20 focus:border-rust focus:ring-rust text-sm">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected($serviceRequest->assigned_technician_id === $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                    <button class="bg-ink-800 hover:bg-ink-700 text-white text-sm font-medium rounded-md px-3 py-2 transition">{{ __('Save') }}</button>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-ink-900/10 p-5">
                <form method="POST" action="{{ route('service-requests.destroy', $serviceRequest) }}" onsubmit="return confirm(@json(__('Delete this ticket permanently?')))">
                    @csrf @method('DELETE')
                    <button class="text-sm text-rust-600 hover:underline">{{ __('Delete ticket') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
