<?php

namespace App\Http\Livewire\Whatsapp;

use App\Models\WhatsappMetaMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MetaChat extends Component
{
    public $conversations  = [];   // sidebar list – one entry per sender_id (phone number)
    public $messages       = [];   // messages for active conversation
    public $selectedId     = null; // active sender_id
    public $newMessage     = '';
    public $search         = '';
    public $editingName    = false;
    public $pendingName    = '';

    protected $listeners = [
        'whatsappMessageReceived' => 'handleIncomingMessage',
        'whatsappRefreshUnread' => 'loadConversations',
    ];

    public function mount(): void
    {
        $this->loadConversations();
    }

    // ─── Sidebar ──────────────────────────────────────────────────────────────

    public function loadConversations(): void
    {
        // Get the latest message per sender plus unread count
        $rows = WhatsappMetaMessage::selectRaw('
                sender_id,
                MAX(sent_at)                                        AS last_at,
                SUM(CASE WHEN from_me = 0 AND is_read = 0 THEN 1 ELSE 0 END) AS unread,
                MAX(message_text)                                   AS last_text
            ')
            ->groupBy('sender_id')
            ->orderByDesc('last_at')
            ->get();

        // Attach sender_name from latest record
        $this->conversations = $rows->map(function ($row) {
            $name = WhatsappMetaMessage::where('sender_id', $row->sender_id)
                ->whereNotNull('sender_name')
                ->orderByDesc('sent_at')
                ->value('sender_name');

            $displayName = $name ?: '+' . $row->sender_id;

            return [
                'id'        => $row->sender_id,
                'name'      => $displayName,
                'last_text' => $row->last_text,
                'last_at'   => $row->last_at,
                'unread'    => (int) $row->unread,
            ];
        })->all();
    }

    public function selectConversation(string $senderId): void
    {
        $this->selectedId = $senderId;
        $this->editingName = false;
        $this->pendingName = '';
        $this->loadMessages();

        // Mark all incoming messages in this thread as read
        WhatsappMetaMessage::where('sender_id', $senderId)
            ->where('from_me', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Refresh sidebar counts
        $this->loadConversations();
        $this->dispatchBrowserEvent('scrollToBottom');
    }

    public function closeConversation(): void
    {
        $this->selectedId = null;
        $this->messages   = [];
        $this->editingName = false;
        $this->pendingName = '';
    }

    // ─── Set Contact Name (manual) ──────────────────────────────────────────

    public function startEditName(): void
    {
        $activeConv = collect($this->conversations)->firstWhere('id', $this->selectedId);
        $current    = $activeConv['name'] ?? '';

        // Pre-fill only if it's a real name (not just formatted phone number)
        $this->pendingName = ($current && $current !== '+' . $this->selectedId) ? $current : '';
        $this->editingName = true;
    }

    public function saveContactName(): void
    {
        $name = trim($this->pendingName);

        if (!$name || !$this->selectedId) {
            $this->editingName = false;
            return;
        }

        // Update sender_name on every row for this customer number
        WhatsappMetaMessage::where('sender_id', $this->selectedId)
            ->update(['sender_name' => $name]);

        $this->editingName = false;
        $this->pendingName = '';
        $this->loadConversations();
    }

    public function cancelEditName(): void
    {
        $this->editingName = false;
        $this->pendingName = '';
    }

    // ─── Messages ─────────────────────────────────────────────────────────────

    public function loadMessages(): void
    {
        if (!$this->selectedId) return;

        $this->messages = WhatsappMetaMessage::forThread($this->selectedId)
            ->get()
            ->map(fn($m) => [
                'id'      => $m->id,
                'text'    => $m->message_text,
                'from_me' => $m->from_me,
                'sent_at' => $m->sent_at?->format('h:i A') ?? '',
            ])
            ->all();
    }

    // ─── Send Reply via Meta Cloud API ───────────────────────────────────────

    public function sendMessage(): void
    {
        if (empty(trim($this->newMessage)) || !$this->selectedId) return;

        $text      = trim($this->newMessage);
        $agentName = Auth::user()->name ?? 'Agent';
        $fullText  = $text . "\n\n« " . $agentName . " »";

        $phoneId = config('services.whatsapp.phone_id');
        $token   = config('services.whatsapp.token');

        if (!$phoneId || !$token) {
            Log::error('WhatsApp Meta API send error: config variables missing in services.php');
            session()->flash('error', 'API configuration error. Phone ID or Access Token is missing.');
            return;
        }

        try {
            $url = "https://graph.facebook.com/v19.0/{$phoneId}/messages";
            $response = Http::withToken($token)->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $this->selectedId,
                'type'              => 'text',
                'text'              => [
                    'preview_url' => false,
                    'body'        => $fullText,
                ],
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $messageId = $responseData['messages'][0]['id'] ?? null;

                // Persist sent message locally so it shows in the console
                WhatsappMetaMessage::create([
                    'sender_id'    => $this->selectedId,
                    'message_text' => $fullText,
                    'message_id'   => $messageId,
                    'from_me'      => true,
                    'is_read'      => true,
                    'sent_at'      => now(),
                ]);

                $this->newMessage = '';
                $this->loadMessages();
                $this->loadConversations();
                $this->dispatchBrowserEvent('scrollToBottom');
            } else {
                Log::error('WhatsApp Meta Cloud API Send failed: ' . $response->body());
                session()->flash('error', 'Failed to send: ' . ($response->json('error.message') ?? 'Meta API error'));
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Meta Cloud API Send exception: ' . $e->getMessage());
            session()->flash('error', 'Could not reach Meta API.');
        }
    }

    // ─── Socket / Listener handlers ──────────────────────────────────────────

    public function handleIncomingMessage($data)
    {
        // If the new message belongs to the current chat, refresh messages
        if ($this->selectedId === ($data['chat']['id'] ?? null)) {
            $this->loadMessages();
            $this->dispatchBrowserEvent('scrollToBottom');
        }
        $this->loadConversations(); // Always refresh conversation list
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $conversations = $this->conversations;

        if ($this->search) {
            $q = strtolower($this->search);
            $conversations = array_filter($conversations, fn($c) =>
                str_contains(strtolower($c['name']), $q) ||
                str_contains(strtolower($c['id']), $q)
            );
        }

        $activeConv = $this->selectedId
            ? collect($this->conversations)->firstWhere('id', $this->selectedId)
            : null;

        return view('livewire.whatsapp.meta-chat', [
            'displayConversations' => array_values($conversations),
            'activeConv'           => $activeConv,
        ]);
    }
}
