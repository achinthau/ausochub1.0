<div class="flex h-screen bg-gray-100 overflow-hidden" style="height: calc(100vh - 64px);">
    <!-- Sidebar -->
    <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
        <!-- Sidebar Header -->
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">WhatsApp</h2>
            <div class="flex space-x-2">
                <!-- Refresh Button -->
                <button wire:click="loadChats" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="p-4">
            <div class="relative">
                <input type="text" wire:model.debounce.300ms="search" placeholder="Search name or number" class="w-full pl-10 pr-4 py-2 bg-gray-100 border-none rounded-lg focus:ring-2 focus:ring-green-500 text-sm transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Chat List -->
        <div class="flex-1 overflow-y-auto">
            @forelse($displayChats as $chat)
                <div 
                    wire:click="selectChat('{{ $chat['id'] }}')" 
                    class="flex items-center p-4 hover:bg-gray-50 cursor-pointer transition-colors {{ $selectedChatId === $chat['id'] ? 'bg-gray-100 font-medium' : '' }}"
                >
                    <div class="relative flex-shrink-0">
                        @if(isset($chat['profilePicUrl']) && $chat['profilePicUrl'])
                            <img src="{{ $chat['profilePicUrl'] }}" alt="{{ $chat['name'] ?? '' }}" class="h-12 w-12 rounded-full object-cover shadow-sm">
                        @else
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ strtoupper(substr($chat['name'] ?? $chat['id'] ?? 'W', 0, 1)) }}
                            </div>
                        @endif
                        @if(($chat['unreadCount'] ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white shadow-sm">
                                {{ $chat['unreadCount'] }}
                            </span>
                        @endif
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="truncate">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $chat['name'] ?: $chat['id'] }}</h3>
                                @if($chat['name'] && $chat['number'])
                                    <p class="text-[10px] text-gray-500 truncate">{{ $chat['number'] }}</p>
                                @endif
                            </div>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap ml-2">
                                {{ \Carbon\Carbon::createFromTimestamp($chat['timestamp'])->format('h:i A') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-0.5">
                            @if($chat['lastMessage'])
                                {{ $chat['lastMessage']['fromMe'] ? 'You: ' : '' }}{{ $chat['lastMessage']['body'] }}
                            @else
                                <span class="italic text-gray-400">No messages yet</span>
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">
                    <p class="text-sm">No chats found</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col bg-[#efeae2]"> <!-- WhatsApp background color -->
        @if($selectedChatId)
            @php 
                $activeChat = collect($chats)->firstWhere('id', $selectedChatId);
            @endphp
            <!-- Chat Header -->
            <div class="p-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between shadow-sm z-10">
                <div class="flex items-center">
                    @if($activeChat && isset($activeChat['profilePicUrl']) && $activeChat['profilePicUrl'])
                        <img src="{{ $activeChat['profilePicUrl'] }}" alt="Profile" class="h-10 w-10 rounded-full object-cover shadow-sm">
                    @else
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold shadow-sm">
                            {{ strtoupper(substr($activeChat['name'] ?? $selectedChatId ?? 'W', 0, 1)) }}
                        </div>
                    @endif
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-gray-900">{{ $activeChat['name'] ?? $selectedChatId }}</h3>
                        <p class="text-[10px] text-gray-500">{{ $activeChat['number'] ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button wire:click="closeChat" class="p-2 hover:bg-gray-200 rounded-full text-gray-400 hover:text-red-500 transition-all" title="Close chat">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-3" wire:poll.5s="loadMessages">
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['fromMe'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-lg p-2.5 shadow-sm text-sm relative {{ $msg['fromMe'] ? 'bg-[#dcf8c6] text-gray-800 rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none' }}">
                            @if($msg['hasMedia'] || (isset($msg['type']) && !in_array($msg['type'], ['chat', 'vcard'])))
                                <div class="mb-2 p-2 bg-black/5 rounded flex items-center space-x-2 border border-black/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] font-medium text-gray-600 uppercase tracking-tight">{{ $msg['type'] ?? 'Media' }}</span>
                                    
                                    <button wire:click.prevent="downloadMedia('{{ $msg['id'] }}')" class="ml-auto p-1 text-gray-500 hover:text-green-600 transition-colors" title="Download">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <p class="whitespace-pre-wrap break-words leading-relaxed">{{ $msg['body'] }}</p>
                            <div class="flex items-center justify-end space-x-1 mt-1 opacity-70">
                                <span class="text-[9px] text-gray-500">
                                    {{ \Carbon\Carbon::createFromTimestamp($msg['timestamp'])->format('h:i A') }}
                                </span>
                                @if($msg['fromMe'])
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input Area -->
            <div class="p-3 bg-gray-50 border-t border-gray-200 z-10">
                @if($attachment)
                    <div class="mb-3 p-3 bg-white rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
                        <div class="flex items-center overflow-hidden">
                            <div class="w-10 h-10 bg-green-100 rounded flex items-center justify-center flex-shrink-0 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-3 truncate">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $attachment->getClientOriginalName() }}</p>
                                <p class="text-[10px] text-gray-500">{{ round($attachment->getSize() / 1024, 1) }} KB</p>
                            </div>
                        </div>
                        <button wire:click="$set('attachment', null)" class="text-red-500 hover:text-red-700 transition-colors p-1 hover:bg-red-50 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                @endif
                <form wire:submit.prevent="sendMessage" class="flex items-center space-x-2">
                    <button type="button" class="p-2 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    
                    <div class="relative">
                        <button type="button" onclick="document.getElementById('whatsapp-attachment-input').click()" class="p-2 text-gray-500 hover:text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                        <input type="file" id="whatsapp-attachment-input" wire:model="attachment" class="hidden">
                        <div wire:loading wire:target="attachment" class="absolute inset-0 flex items-center justify-center bg-white/50 rounded">
                            <svg class="animate-spin h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <input 
                        type="text" 
                        wire:model.defer="newMessage" 
                        placeholder="Type a message" 
                        class="flex-1 bg-white border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm text-sm transition-all"
                    >
                    <button type="submit" class="p-2.5 bg-green-500 text-white rounded-full hover:bg-green-600 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-500">
                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 shadow-md border border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">WhatsApp Messaging</h2>
                <p class="max-w-xs text-center text-gray-500 text-sm leading-relaxed">Integrated WhatsApp Messaging with Auso CallHUB.</p>
                <div class="mt-12 flex items-center text-xs text-gray-400 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    End-to-end encrypted
                </div>
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('scrollToBottom', event => {
            const container = document.getElementById('messages-container');
            if (container) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);
            }
        });

        // Initialize scroll for first load
        document.addEventListener('livewire:load', function () {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }

            // Listen for WhatsApp messages via global presenceSocket
            if (typeof presenceSocket !== 'undefined') {
                presenceSocket.on('whatsapp.message', (data) => {
                    console.log('WhatsApp message received via socket:', data);
                    // Emit Livewire event
                    Livewire.emit('whatsappMessageReceived', data.lead);
                    // Also trigger unread count refresh
                    Livewire.emit('whatsappRefreshUnread');
                });
            }
        });
    </script>
</div>
