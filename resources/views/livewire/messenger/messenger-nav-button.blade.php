<div wire:poll.30s="loadUnreadCount">
    <a href="{{ route('messenger.chat') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border transition {{ request()->routeIs('messenger.chat') ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-blue-600 hover:border-blue-200 hover:bg-blue-50' }} {{ $unreadCount > 0 ? 'animate-pulse' : '' }}" title="Messenger">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
            <path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.906 1.378 5.504 3.538 7.26V22l3.236-1.794A10.764 10.764 0 0 0 12 20.486c5.523 0 10-4.145 10-9.243S17.523 2 12 2zm1.007 12.443-2.548-2.72-4.975 2.72 5.473-5.81 2.61 2.72 4.912-2.72-5.472 5.81z" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute -right-1 -top-1 min-w-[1.25rem] rounded-full bg-blue-600 px-1.5 py-0.5 text-center text-[10px] font-semibold leading-none text-white shadow-sm">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
</div>