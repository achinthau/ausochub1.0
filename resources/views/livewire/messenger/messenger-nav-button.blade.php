<div class="hidden space-x-8 sm:-my-px sm:ml-10 pt-4 sm:flex" wire:poll.30s="loadUnreadCount">
    <x-jet-nav-link href="{{ route('messenger.chat') }}" :active="request()->routeIs('messenger.chat')"
        class="flex items-center gap-1 {{ $unreadCount > 0 ? 'text-blue-600 animate-pulse' : 'text-blue-500' }}">

        {{-- Facebook Messenger icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path
                d="M12 2C6.477 2 2 6.145 2 11.243c0 2.906 1.378 5.504 3.538 7.26V22l3.236-1.794A10.764 10.764 0 0 0 12 20.486c5.523 0 10-4.145 10-9.243S17.523 2 12 2zm1.007 12.443-2.548-2.72-4.975 2.72 5.473-5.81 2.61 2.72 4.912-2.72-5.472 5.81z" />
        </svg>

        <!-- <span class="text-xs font-medium">Messenger</span> -->

        @if ($unreadCount > 0)
            <span class="ml-1 bg-blue-600 text-white px-1.5 py-0.5 text-[10px] font-bold rounded-full shadow-sm">
                {{ $unreadCount }}
            </span>
        @endif
    </x-jet-nav-link>
</div>