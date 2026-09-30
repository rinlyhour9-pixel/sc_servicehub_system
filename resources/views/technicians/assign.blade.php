@extends('layouts.app')
@section('title', __('Assign Technician'))
@section('content')
    <div class="bg-white rounded-lg border border-ink-900/10 p-6">
        <h2 class="font-display text-lg font-semibold mb-4">{{ __('Assign Technician') }}</h2>
        <p class="text-sm text-ink-900/60 mb-4">{{ __('Quickly assign technicians to open service requests.') }}</p>
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-paper p-4 rounded">
                <form method="POST" action="{{ route('technicians.assign.store') }}">
                    @csrf
                    <label class="block text-sm mb-1">{{ __('Service Request') }}</label>
                    <select name="request_id" id="request-select" class="w-full rounded border-ink-900/20 mb-3">
                        @foreach ($requests as $r)
                            <option value="{{ $r->id }}" data-category="{{ $r->service_category_id }}">{{ $r->ticket_number }} — {{ $r->title }}
                                ({{ $r->customer->name }})
                            </option>
                        @endforeach
                    </select>
                    <label class="block text-sm mb-1">{{ __('Technician') }}</label>
                    <select name="technician_id" id="technician-select" class="w-full rounded border-ink-900/20 mb-1">
                        <option value="">{{ __('Select technician') }}</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" data-categories="{{ $tech->serviceCategories->pluck('id')->implode(',') }}">{{ $tech->name }} — {{ $tech->phone }}</option>
                        @endforeach
                    </select>
                    <p class="mb-3 text-xs text-ink-900/40">{{ __('Only technicians skilled in the selected request\'s category are shown.') }}</p>
                    <button class="bg-rust text-white px-3 py-2 rounded">{{ __('Assign') }}</button>
                </form>

                <script>
                    (function () {
                        const requestSelect = document.getElementById('request-select');
                        const technicianSelect = document.getElementById('technician-select');
                        if (!requestSelect || !technicianSelect) return;
                        const options = Array.from(technicianSelect.options);

                        function applyFilter() {
                            const selected = requestSelect.options[requestSelect.selectedIndex];
                            const categoryId = selected ? selected.dataset.category : '';
                            options.forEach(opt => {
                                if (!opt.value) return;
                                const skills = (opt.dataset.categories || '').split(',').filter(Boolean);
                                const matches = !categoryId || skills.length === 0 || skills.includes(categoryId);
                                opt.hidden = !matches;
                                opt.disabled = !matches;
                            });
                            const currentTech = technicianSelect.options[technicianSelect.selectedIndex];
                            if (currentTech && currentTech.hidden) {
                                technicianSelect.value = '';
                            }
                        }

                        requestSelect.addEventListener('change', applyFilter);
                        applyFilter();
                    })();
                </script>
            </div>
        </div>
    </div>
@endsection
