<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex pt-4" wire:poll.30s="loadUnreadCount">
    <x-jet-nav-link href="{{ route('whatsapp.chat') }}" :active="request()->routeIs('whatsapp.chat')"
        class="text-green-600 flex items-center gap-1 {{ $unreadCount > 0 ? 'animate-pulse' : '' }}">

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-green-600">
            <path
                d="M12.01 2c-5.52 0-10 4.48-10 10a9.96 9.96 0 0 0 1.25 4.82L2 22l5.36-1.41A9.91 9.91 0 0 0 12.01 22c5.52 0 10-4.48 10-10s-4.48-10-10-10zm0 1.66c4.6 0 8.34 3.74 8.34 8.34s-3.74 8.34-8.34 8.34c-1.57 0-3.04-.43-4.3-1.19l-.31-.18-3.19.84.85-3.11-.2-.32a8.3 8.3 0 0 1-1.19-4.38c0-4.6 3.74-8.34 8.34-8.34zM8.83 7.35a.6.6 0 0 0-.42.2c-.17.18-.58.57-.58 1.39s.6 1.62.68 1.73c.09.12 1.18 1.81 2.87 2.54.4.17.72.28.97.36.41.13.78.11 1.07.07.32-.05 1-.41 1.14-.81.14-.4.14-.75.1-.82-.04-.07-.15-.11-.32-.19s-1-.49-1.15-.55-.27-.08-.38.09-.45.55-.55.67-.21.13-.38.05a4.77 4.77 0 0 1-1.42-.88 5.25 5.25 0 0 1-.98-1.22c-.1-.17-.01-.26.08-.35.08-.08.17-.2.25-.3.09-.09.12-.16.18-.27.06-.11.03-.21-.01-.3-.04-.08-.38-.92-.52-1.26-.14-.35-.29-.3-.38-.3z" />
        </svg>

        @if ($unreadCount > 0)
            <span class="ml-2 bg-green-500 text-white px-2 py-0.5 text-[10px] rounded-full shadow-sm">
                {{ $unreadCount }}
            </span>
        @endif
    </x-jet-nav-link>
</div>