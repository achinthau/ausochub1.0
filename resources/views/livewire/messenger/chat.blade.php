<div class="flex bg-gray-100 overflow-hidden" style="height: calc(100vh - 64px);">

    {{-- ═══════════════════════════════════════════════════
         SIDEBAR – Conversation list
    ══════════════════════════════════════════════════════ --}}
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">

        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-500 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-6 h-6">
                    <path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.906 1.378 5.504 3.538 7.26V22l3.236-1.794A10.764 10.764 0 0 0 12 20.486c5.523 0 10-4.145 10-9.243S17.523 2 12 2zm1.007 12.443-2.548-2.72-4.975 2.72 5.473-5.81 2.61 2.72 4.912-2.72-5.472 5.81z"/>
                </svg>
                <h2 class="text-white font-semibold text-base">Messenger</h2>
            </div>
            <button wire:click="loadConversations"
                    class="p-1.5 rounded-full hover:bg-blue-700 text-white transition-colors"
                    title="Refresh">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="px-3 py-2 border-b border-gray-100">
            <div class="relative">
                <input type="text"
                       wire:model.debounce.300ms="search"
                       placeholder="Search conversations…"
                       class="w-full pl-9 pr-3 py-1.5 bg-gray-100 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all border-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2 text-gray-400"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Conversation List --}}
        <div class="flex-1 overflow-y-auto divide-y divide-gray-50" wire:poll.10s="loadConversations">
            @forelse($displayConversations as $conv)
                <div wire:click="selectConversation('{{ $conv['id'] }}')"
                     class="flex items-center px-3 py-3 cursor-pointer transition-colors
                            {{ $selectedId === $conv['id']
                                ? 'bg-blue-50 border-l-4 border-blue-500'
                                : 'hover:bg-gray-50 border-l-4 border-transparent' }}">

                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <div class="h-11 w-11 rounded-full bg-gradient-to-br from-blue-400 to-blue-600
                                    flex items-center justify-center text-white font-bold text-base shadow-sm">
                            {{ strtoupper(substr($conv['name'], 0, 1)) }}
                        </div>
                        @if ($conv['unread'] > 0)
                            <span class="absolute -top-1 -right-1 bg-blue-600 text-white text-[9px] font-bold
                                         min-w-[16px] h-4 flex items-center justify-center
                                         rounded-full border-2 border-white shadow-sm px-1">
                                {{ $conv['unread'] }}
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 truncate {{ $conv['unread'] > 0 ? 'text-blue-700' : '' }}">
                                {{ $conv['name'] }}
                            </p>
                            @if($conv['last_at'])
                                <span class="text-[10px] text-gray-400 whitespace-nowrap ml-1">
                                    {{ \Carbon\Carbon::parse($conv['last_at'])->format('h:i A') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-0.5 {{ $conv['unread'] > 0 ? 'font-medium text-gray-700' : '' }}">
                            {{ $conv['last_text'] ?? 'No messages yet' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-gray-400 px-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-sm">No conversations yet</p>
                    <p class="text-xs mt-1 text-gray-400">Messages from Facebook will appear here</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         MAIN CHAT AREA
    ══════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col" style="background: #f0f2f5;">

        @if($selectedId && $activeConv)

            {{-- Chat Header --}}
            <div class="px-4 py-3 bg-white border-b border-gray-200 flex items-center justify-between shadow-sm z-10">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600
                                flex items-center justify-center text-white font-bold shadow-sm">
                        {{ strtoupper(substr($activeConv['name'], 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        @if($editingName)
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       wire:model.defer="pendingName"
                                       wire:keydown.enter="saveContactName"
                                       wire:keydown.escape="cancelEditName"
                                       placeholder="Enter sender's name..."
                                       class="px-2 py-1 text-sm bg-gray-50 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 font-medium text-gray-900"
                                       autoFocus>
                                <button wire:click="saveContactName" class="p-1 text-green-600 hover:bg-green-50 rounded" title="Save">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                                <button wire:click="cancelEditName" class="p-1 text-gray-400 hover:bg-gray-100 rounded" title="Cancel">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center gap-2 group">
                                <h3 class="text-sm font-bold text-gray-900">{{ $activeConv['name'] }}</h3>
                                {{-- Pencil: edit name manually --}}
                                <button wire:click="startEditName" class="p-1 text-gray-400 hover:text-blue-600 rounded hover:bg-gray-100 opacity-0 group-hover:opacity-100 transition-opacity" title="Edit name manually">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                {{-- Cloud icon: try fetching from Meta API --}}
                                <button wire:click="fetchNameFromApi" wire:loading.attr="disabled" title="Try fetching name from Meta Graph API"
                                        class="p-1 text-gray-400 hover:text-indigo-600 rounded hover:bg-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                        <p class="text-[11px] text-gray-500 mt-0.5">PSID: {{ $activeConv['id'] }}</p>
                    </div>
                </div>
                <button wire:click="closeConversation"
                        class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-red-500 transition-colors"
                        title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mx-4 mt-3 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 mt-3 px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Messages --}}
            <div id="messages-container"
                 class="flex-1 overflow-y-auto px-4 py-4 space-y-2"
                 wire:poll.8s="loadMessages">

                @forelse($messages as $msg)
                    <div class="flex {{ $msg['from_me'] ? 'justify-end' : 'justify-start' }} items-end gap-2">

                        {{-- Incoming avatar --}}
                        @if(!$msg['from_me'])
                            <div class="h-7 w-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600
                                        flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mb-1 shadow-sm">
                                {{ strtoupper(substr($activeConv['name'], 0, 1)) }}
                            </div>
                        @endif

                        {{-- Bubble --}}
                        <div class="max-w-[65%]">
                            <div class="px-3 py-2 rounded-2xl shadow-sm text-sm leading-relaxed
                                        {{ $msg['from_me']
                                            ? 'bg-blue-600 text-white rounded-br-sm'
                                            : 'bg-white text-gray-800 rounded-bl-sm' }}">
                                <p class="whitespace-pre-wrap break-words">{{ $msg['text'] }}</p>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5 {{ $msg['from_me'] ? 'text-right' : 'text-left' }} px-1">
                                {{ $msg['sent_at'] }}
                                @if($msg['from_me'])
                                    <span class="ml-1 text-blue-400">✓✓</span>
                                @endif
                            </p>
                        </div>

                        {{-- Sent avatar (agent initials) --}}
                        @if($msg['from_me'])
                            <div class="h-7 w-7 rounded-full bg-gray-400
                                        flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mb-1 shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-400 text-sm">No messages in this conversation yet.</p>
                    </div>
                @endforelse
            </div>

            {{-- Reply Input --}}
            <div class="px-4 py-3 bg-white border-t border-gray-200 z-10">
                <form wire:submit.prevent="sendMessage" class="flex items-end gap-2">
                    <div class="flex-1 bg-gray-100 rounded-2xl px-4 py-2 flex items-end">
                        <textarea
                            wire:model.defer="newMessage"
                            placeholder="Reply to {{ $activeConv['name'] }}…"
                            rows="1"
                            id="messenger-reply-input"
                            class="flex-1 bg-transparent resize-none focus:outline-none text-sm text-gray-800 placeholder-gray-400 max-h-32"
                            style="min-height:24px;"
                            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.closest('form').dispatchEvent(new Event('submit',{bubbles:true,cancelable:true}));}"
                        ></textarea>
                    </div>

                    {{-- Send Button --}}
                    <button type="submit"
                            class="flex-shrink-0 w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white
                                   rounded-full flex items-center justify-center shadow-md transition-all
                                   active:scale-95 disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>

                {{-- Sending indicator --}}
                <div wire:loading wire:target="sendMessage" class="mt-1 flex items-center gap-1 text-[11px] text-blue-500">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Sending…
                </div>
            </div>

        @else

            {{-- Empty state --}}
            <div class="flex-1 flex flex-col items-center justify-center text-center px-6">
                <div class="w-28 h-28 rounded-full bg-white shadow-md border border-gray-100
                            flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2563EB" class="w-14 h-14">
                        <path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.906 1.378 5.504 3.538 7.26V22l3.236-1.794A10.764 10.764 0 0 0 12 20.486c5.523 0 10-4.145 10-9.243S17.523 2 12 2zm1.007 12.443-2.548-2.72-4.975 2.72 5.473-5.81 2.61 2.72 4.912-2.72-5.472 5.81z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Facebook Messenger</h2>
                <p class="text-gray-500 text-sm max-w-xs leading-relaxed">
                    Select a conversation from the left to view messages and reply to your customers.
                </p>
                <div class="mt-8 px-4 py-2 bg-white rounded-full border border-gray-100 shadow-sm
                            flex items-center gap-2 text-xs text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Messages auto-refresh every 10 seconds
                </div>
            </div>

        @endif
    </div>

    {{-- Scroll-to-bottom script --}}
    <script>
        window.addEventListener('scrollToBottom', () => {
            const c = document.getElementById('messages-container');
            if (c) setTimeout(() => { c.scrollTop = c.scrollHeight; }, 80);
        });

        document.addEventListener('livewire:load', () => {
            const c = document.getElementById('messages-container');
            if (c) c.scrollTop = c.scrollHeight;
        });
    </script>
</div>
