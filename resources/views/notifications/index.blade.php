@extends('layouts.app')
@section('title', __('Notifications'))
@section('content')
    <button id="enable-phone-alerts" class="mb-4 border border-ink-900/20 rounded-md px-4 py-2 text-sm hover:bg-white">{{ __('Enable phone/browser alerts') }}</button>
    <div class="max-w-3xl bg-white rounded-lg border border-ink-900/10 divide-y divide-ink-900/5">
        @forelse($notifications as $notification)
            <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-5 py-4 hover:bg-paper">
                <p class="text-sm font-medium">{{ $notification->data['message'] }}</p>
                <p class="text-xs text-ink-900/45 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </a>
        @empty <p class="px-5 py-8 text-sm text-ink-900/45">{{ __('You have no notifications.') }}</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
    <script>document.getElementById('enable-phone-alerts').addEventListener('click',async()=>{if(!('Notification'in window))return alert(@json(__('This browser does not support notifications.')));const result=await Notification.requestPermission();alert(result==='granted'?@json(__('Phone/browser alerts enabled.')):@json(__('Notification permission was not granted.')));});</script>
@endsection
