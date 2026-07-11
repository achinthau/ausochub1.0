<div wire:poll.3000ms="loadMessagesCount">
    <a href="{{ route('chat.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border transition {{ request()->routeIs('chat.index') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50' }} {{ $messagesCount > 0 ? 'animate-pulse' : '' }}" title="Chat">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h6m-9 8.25h11.379a3 3 0 0 0 2.122-.879l2.12-2.121a3 3 0 0 0 .879-2.121V6.75a3 3 0 0 0-3-3h-12a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3Z" />
        </svg>

        @if ($messagesCount > 0)
            <span class="absolute -right-1 -top-1 min-w-[1.25rem] rounded-full bg-emerald-600 px-1.5 py-0.5 text-center text-[10px] font-semibold leading-none text-white shadow-sm">
                {{ $messagesCount }}
            </span>
        @endif
    </a>
</div>
