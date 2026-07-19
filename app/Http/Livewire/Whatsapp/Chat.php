<?php

namespace App\Http\Livewire\Whatsapp;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    use WithFileUploads;

    public $chats = [];
    public $messages = [];
    public $selectedChatId = null;
    public $newMessage = '';
    public $search = '';
    public $attachment;
    public $whatsappServiceUrl;

    protected $listeners = [
        'refreshMessages' => '$refresh',
        'whatsappMessageReceived' => 'handleIncomingMessage'
    ];

    public function mount()
    {
        $this->whatsappServiceUrl = config('services.whatsapp.webjs_url', 'http://localhost:3001');
        $this->loadChats();
    }

    public function loadChats()
    {
        try {
            $response = Http::timeout(30)->get($this->whatsappServiceUrl . '/chats');
            if ($response->successful()) {
                $this->chats = $response->json();
            } else {
//                 Log::error('Failed to fetch WhatsApp chats', ['error' => $response->body()]);
            }
        } catch (\Exception $e) {
//             Log::error('WhatsApp Chat Service Error: ' . $e->getMessage());
        }
    }

    public function closeChat()
    {
        $this->selectedChatId = null;
        $this->messages = [];
    }

    public function selectChat($chatId)
    {
        $this->selectedChatId = $chatId;
        $this->loadMessages();
        $this->dispatchBrowserEvent('scrollToBottom');
    }

    public function loadMessages()
    {
        if (!$this->selectedChatId) return;

        try {
            $response = Http::timeout(30)->get($this->whatsappServiceUrl . '/messages/' . $this->selectedChatId);
            if ($response->successful()) {
                $this->messages = $response->json();
            }
        } catch (\Exception $e) {
//             Log::error('WhatsApp Message Fetch Error: ' . $e->getMessage());
        }
    }

    public function sendMessage()
    {
        $user = Auth::user()->name;
        if (empty($this->newMessage) && !$this->attachment) return;
        if (!$this->selectedChatId) return;

        try {
            $postData = [
                'to' => $this->selectedChatId,
                'message' => $this->newMessage . "\n" . "<< " . $user . " >>"
            ];

            if ($this->attachment) {
//                 Log::info('WhatsApp Attachment detected', [
//                     'name' => $this->attachment->getClientOriginalName(),
//                     'mime' => $this->attachment->getMimeType(),
//                     'size' => $this->attachment->getSize()
//                 ]);
                $postData['attachment'] = [
                    'base64' => base64_encode(file_get_contents($this->attachment->getRealPath())),
                    'mimetype' => $this->attachment->getMimeType(),
                    'filename' => $this->attachment->getClientOriginalName(),
                ];
            }

//             Log::info('Sending WhatsApp message via service', ['target' => $this->selectedChatId, 'has_attachment' => !!$this->attachment]);
            $response = Http::timeout(60)->post($this->whatsappServiceUrl . '/send-message', $postData);

            if ($response->successful()) {
//                 Log::info('WhatsApp message sent successfully');
                
                // Optimistically append message
                $responseData = $response->json()['data'];
                $newMessage = [
                    'id' => $responseData['id']['_serialized'] ?? $responseData['id'],
                    'body' => $responseData['body'] ?? $this->newMessage,
                    'timestamp' => $responseData['timestamp'] ?? time(),
                    'fromMe' => true,
                    'hasMedia' => $responseData['hasMedia'] ?? !!$this->attachment,
                    'type' => $responseData['type'] ?? ($this->attachment ? 'image' : 'chat')
                ];
                
                $this->messages[] = $newMessage;
                
                $this->newMessage = '';
                $this->attachment = null;
                // $this->loadMessages(); // Removed to prevent reload/delay
                $this->loadChats();    // Refresh chat list (last message)
                $this->dispatchBrowserEvent('scrollToBottom');
            } else {
//                 Log::error('Failed to send WhatsApp message', [
//                     'status' => $response->status(),
//                     'body' => $response->body()
//                 ]);
            }
        } catch (\Exception $e) {
//             Log::error('WhatsApp Send Error: ' . $e->getMessage());
        }
    }

    public function handleIncomingMessage($data)
    {
        // If the new message belongs to the current chat, refresh messages
        if ($this->selectedChatId === ($data['chat']['id'] ?? null)) {
            $this->loadMessages();
            $this->dispatchBrowserEvent('scrollToBottom');
        }
        $this->loadChats(); // Always refresh chat list
    }

    public function downloadMedia($msgId)
    {
        try {
            $response = Http::timeout(30)->get($this->whatsappServiceUrl . '/message/media', [
                'msgId' => $msgId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $fileContent = base64_decode($data['data']);
                $filename = $data['filename'] ?? 'download-' . time() . '.' . Str::after($data['mimetype'], '/');

                return response()->streamDownload(function () use ($fileContent) {
                    echo $fileContent;
                }, $filename, [
                    'Content-Type' => $data['mimetype'],
                ]);
            } else {
                session()->flash('error', 'Failed to download media: ' . ($response->json()['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
//             Log::error('Media Download Error: ' . $e->getMessage());
            session()->flash('error', 'Error downloading media.');
        }
    }

    public function render()
    {
        $filteredChats = $this->chats;
        if ($this->search) {
            $filteredChats = collect($this->chats)->filter(function($chat) {
                return str_contains(strtolower($chat['name'] ?? ''), strtolower($this->search)) || 
                       str_contains(strtolower($chat['number'] ?? ''), strtolower($this->search)) ||
                       str_contains(strtolower($chat['id'] ?? ''), strtolower($this->search));
            })->values()->all();
        }

        return view('livewire.whatsapp.chat', [
            'displayChats' => $filteredChats
        ]);
    }
}
