@php
    $customersActive = request()->routeIs('customers.*');
    $techniciansActive = request()->routeIs('technicians.*');
@endphp

<a href="{{ route('dashboard') }}"
    class="{{ $navItem('dashboard', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Dashboard' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Dashboard') }}</span>
</a>

<a href="{{ route('notifications.index') }}"
    class="{{ $navItem('notifications', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Alerts' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Alerts') }}</span>
    @auth @if (auth()->user()->unreadNotifications()->count())
        <span x-show="sidebarOpen" x-cloak
            class="ml-auto rounded-full bg-amber-400 px-2 py-0.5 text-xs text-[#342f73]">{{ auth()->user()->unreadNotifications()->count() }}</span>
    @endif @endauth
</a>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Service Requests') }}</span>
</p>

<a href="{{ route('service-requests.board') }}"
    class="{{ $navItem('service-requests.board', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Pipeline%20Board' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="5" height="16" rx="1" />
            <rect x="10" y="4" width="5" height="10" rx="1" />
            <rect x="17" y="4" width="5" height="13" rx="1" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Pipeline Board') }}</span>
</a>

<a href="{{ route('service-requests.index') }}"
    class="{{ $navItem('service-requests', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Service%20Requests' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14.7 6.3a1 1 0 0 0-1.4 0l-7 7a1 1 0 0 0 0 1.4l3.3 3.3a1 1 0 0 0 1.4 0l7-7a1 1 0 0 0 0-1.4z" />
            <path d="m5 19-2 2M15 5l4-4 4 4-4 4z" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Service Requests') }}</span>
</a>

<a href="{{ route('service-requests.create') }}"
    class="{{ $navItem('service-requests.create', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Create%20Work%20Order' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 11h18M12 15v4M10 17h4" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Create Work Order') }}</span>
</a>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Technicians') }}</span>
</p>
<div x-data="{ open: {{ $techniciansActive ? 'true' : 'false' }} }" class="px-2">
    <button @click="open = !open" type="button"
        class="w-full flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-left transition
            {{ $techniciansActive ? 'bg-amber-400 text-[#342f73] font-semibold shadow-sm ring-1 ring-amber-200' : 'text-white/80 hover:bg-white/10' }}">
        <span class="flex items-center gap-3">
            <span
                class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md {{ $techniciansActive ? 'bg-[#342f73]/10' : 'bg-white/10' }}">
                <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </span>
            <span class="font-medium tracking-wide text-sm truncate" x-show="sidebarOpen" x-cloak>{{ __('Technicians') }}</span>
        </span>
        <svg x-show="sidebarOpen" x-cloak class="h-4 w-4 shrink-0 transition-transform"
            :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div x-show="open && sidebarOpen" x-transition class="mt-1 bg-white rounded-lg shadow-md p-1.5 space-y-0.5">
        <a href="{{ route('technicians.index') }}"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">◌</span>
            <span>{{ __('Technician List') }}</span>
        </a>

        <a href="{{ route('technicians.create') }}"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">＋</span>
            <span>{{ __('New Technician') }}</span>
        </a>

        <a href="{{ route('technicians.assign') }}"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">⇄</span>
            <span>{{ __('Assign Technician') }}</span>
        </a>

        <a href="{{ route('technicians.complete') }}"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">✓</span>
            <span>{{ __('Technician Work') }}</span>
        </a>
    </div>
</div>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Customers') }}</span>
</p>
<div x-data="{ open: {{ $customersActive ? 'true' : 'false' }} }" class="px-2">
    <button @click="open = !open" type="button"
        class="w-full flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-left transition
            {{ $customersActive ? 'bg-amber-400 text-[#342f73] font-semibold shadow-sm ring-1 ring-amber-200' : 'text-white/80 hover:bg-white/10' }}">
        <span class="flex items-center gap-3">
            <span
                class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md {{ $customersActive ? 'bg-[#342f73]/10' : 'bg-white/10' }}">
                <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </span>
            <span class="font-medium tracking-wide text-sm truncate" x-show="sidebarOpen" x-cloak>{{ __('Customers') }}</span>
        </span>
        <svg x-show="sidebarOpen" x-cloak class="h-4 w-4 shrink-0 transition-transform"
            :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div x-show="open && sidebarOpen" x-transition class="mt-1 bg-white rounded-lg shadow-md p-1.5 space-y-0.5">
        <a href="{{ route('customers.index') }}"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">◌</span>
            <span>{{ __('Customer List') }}</span>
        </a>

        <a href="{{ route('customers.create') }}"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">＋</span>
            <span>{{ __('New Customer') }}</span>
        </a>

        <a href="{{ route('customers.index') }}?supplier=1"
            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-ink-900/80 hover:bg-paper">
            <span class="flex h-5 w-5 items-center justify-center text-base leading-none text-ink-900/40">≣</span>
            <span>{{ __('Supplier List') }}</span>
        </a>
    </div>
</div>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Billing') }}</span>
</p>
<a href="{{ route('invoices.index') }}"
    class="{{ $navItem('invoices', $compact ?? false) }} flex items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Invoices%20%26%20Payments' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="M4 3h16v18l-3-2-3 2-3-2-3 2-4-2z" />
            <path d="M8 9h8M8 13h8" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Invoices & Payments') }}</span>
</a>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Cash & Day') }}</span>
</p>
<a href="{{ route('wallet.index') }}"
    class="{{ $navItem('wallet', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Wallet%20%26%20Cash' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <rect x="2" y="5" width="20" height="14" rx="2" />
            <path d="M2 10h20M7 15h3" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Wallet & Cash') }}</span>
</a>

<a href="{{ route('daily-operations.index') }}"
    class="{{ $navItem('daily-operations', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Open%20%2F%20End%20Day' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 11h18" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Open / End Day') }}</span>
</a>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Reports') }}</span>
</p>
<a href="{{ route('reports.index') }}"
    class="{{ $navItem('reports', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Reports' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 3v18h18" />
            <path d="M7 15l4-6 3 3 5-7" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Reports') }}</span>
</a>

<p class="mt-5 mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-white/55">
    <span x-show="sidebarOpen" x-cloak>{{ __('Administration') }}</span>
</p>
<a href="{{ route('categories.index') }}"
    class="{{ $navItem('categories', $compact ?? false) }} flex items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Service%20Categories' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <circle cx="12" cy="12" r="3" />
            <path d="M12 2v3m0 14v3M2 12h3m14 0h3M4.9 4.9 7 7m10 10 2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Service Categories') }}</span>
</a>

@php($manageUsersActive = request()->routeIs('manage-users.*') || request()->routeIs('users.*'))
<a href="{{ route('manage-users.index') }}"
    class="group flex w-full items-center justify-start rounded-lg px-3 py-2.5 mb-1 transition text-left {{ $manageUsersActive ? 'bg-amber-400 text-[#342f73] font-semibold shadow-sm ring-1 ring-amber-200' : 'text-white/80' }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Manage%20Users' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Manage Users') }}</span>
</a>

<a href="{{ route('business-settings.edit') }}"
    class="{{ $navItem('business-settings', $compact ?? false) }} flex w-full items-center gap-3 px-3 py-2"
    {{ $compact ?? false ? 'title=Business%20Name%20%26%20Logo' : '' }}>
    <span class="shrink-0 flex items-center justify-center h-6 w-6 rounded-md bg-white/5 text-white/80">
        <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16v16H4z" />
            <path d="M4 15l4-4 4 4 4-6 4 6" />
        </svg>
    </span>
    <span class="truncate" x-show="sidebarOpen" x-cloak>{{ __('Business Name & Logo') }}</span>
</a>
