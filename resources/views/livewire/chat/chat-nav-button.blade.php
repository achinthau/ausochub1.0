<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex" wire:poll.3000ms="loadMessagesCount">
    <x-jet-nav-link href="{{ route('chat.index') }}" :active="request()->routeIs('chat.index')"
        class="{{ $messagesCount > 0 ? 'text-green-500 font-bold' : '' }}">
        {{ __('Chat') }}
        @if ($messagesCount > 0)
            <span class="ml-2 bg-green-500 text-white px-2 py-1 text-xs rounded-full">
                {{ $messagesCount }}
            </span>
        @endif
    </x-jet-nav-link>
</div>
