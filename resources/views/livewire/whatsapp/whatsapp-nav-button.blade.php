<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex" wire:poll.30s="loadUnreadCount">
    <x-jet-nav-link href="{{ route('whatsapp.chat') }}" :active="request()->routeIs('whatsapp.chat')" 
        class="text-green-600 {{ $unreadCount > 0 ? 'animate-pulse' : '' }}">
        {{ __('WhatsApp') }}
        @if ($unreadCount > 0)
            <span class="ml-2 bg-green-500 text-white px-2 py-0.5 text-[10px] rounded-full shadow-sm">
                {{ $unreadCount }}
            </span>
        @endif
    </x-jet-nav-link>
</div>
