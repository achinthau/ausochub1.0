<?php

namespace App\Http\Livewire\Messenger;

use App\Models\MessengerMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Chat extends Component
{
    public $conversations  = [];   // sidebar list – one entry per sender_id
    public $messages       = [];   // messages for active conversation
    public $selectedId     = null; // active sender_id
    public $newMessage     = '';
    public $search         = '';
    public $editingName    = false;
    public $pendingName    = '';

    protected $listeners = [
        'messengerRefresh' => 'loadConversations',
    ];

    public function mount(): void
    {
        $this->loadConversations();
    }

    // ─── Sidebar ──────────────────────────────────────────────────────────────

    public function loadConversations(): void
    {
        // Get the latest message per sender plus unread count
        $rows = MessengerMessage::selectRaw('
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
            $name = MessengerMessage::where('sender_id', $row->sender_id)
                ->whereNotNull('sender_name')
                ->orderByDesc('sent_at')
                ->value('sender_name');

            $displayName = $name ?: $row->sender_id;
            if ($displayName === 'Facebook User') {
                $displayName = 'Facebook User (' . substr($row->sender_id, -4) . ')';
            }

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
        MessengerMessage::where('sender_id', $senderId)
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

    // ─── Set Contact Name (manual – no Graph API needed) ─────────────────────

    public function startEditName(): void
    {
        $activeConv = collect($this->conversations)->firstWhere('id', $this->selectedId);
        $current    = $activeConv['name'] ?? '';

        // Pre-fill only if already has a real name (not raw PSID or "Facebook User")
        $this->pendingName = ($current && $current !== $this->selectedId && $current !== 'Facebook User') ? $current : '';
        $this->editingName = true;
    }

    public function saveContactName(): void
    {
        $name = trim($this->pendingName);

        if (!$name || !$this->selectedId) {
            $this->editingName = false;
            return;
        }

        // Update sender_name on every row for this PSID
        MessengerMessage::where('sender_id', $this->selectedId)
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

    // ─── Try fetching name from Graph API on-demand ───────────────────────────

    public function fetchNameFromApi(): void
    {
        if (!$this->selectedId) return;

        $token = env('FACEBOOK_PAGE_ACCESS_TOKEN');

        try {
            $response = Http::timeout(5)->get(
                "https://graph.facebook.com/v19.0/{$this->selectedId}",
                ['fields' => 'name', 'access_token' => $token]
            );

            if ($response->successful()) {
                $data = $response->json();
                $name = $data['name']
                    ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

                if ($name) {
                    MessengerMessage::where('sender_id', $this->selectedId)
                        ->update(['sender_name' => $name]);
                    session()->flash('success', "Name resolved: {$name}");
                    $this->loadConversations();
                    return;
                }
            }

            session()->flash('error', 'API responded but returned no name. Check app permissions.');

        } catch (\Exception $e) {
            session()->flash('error', 'Could not reach Meta API — network timeout. Apply the MTU fix first.');
        }
    }

    // ─── Messages ─────────────────────────────────────────────────────────────

    public function loadMessages(): void
    {
        if (!$this->selectedId) return;

        $this->messages = MessengerMessage::forThread($this->selectedId)
            ->get()
            ->map(fn($m) => [
                'id'      => $m->id,
                'text'    => $m->message_text,
                'from_me' => $m->from_me,
                'sent_at' => $m->sent_at?->format('h:i A') ?? '',
            ])
            ->all();
    }

    // ─── Send Reply ───────────────────────────────────────────────────────────

    public function sendMessage(): void
    {
        if (empty(trim($this->newMessage)) || !$this->selectedId) return;

        $text      = trim($this->newMessage);
        $agentName = Auth::user()->name ?? 'Agent';
        $fullText  = $text . "\n\n« " . $agentName . " »";

        try {
            $response = Http::withToken(env('FACEBOOK_PAGE_ACCESS_TOKEN'))
                ->post('https://graph.facebook.com/v19.0/me/messages', [
                    'recipient'      => ['id' => $this->selectedId],
                    'message'        => ['text' => $fullText],
                    'messaging_type' => 'RESPONSE',
                ]);

            if ($response->successful()) {
                // Persist sent message locally so it shows in the console
                MessengerMessage::create([
                    'sender_id'    => $this->selectedId,
                    'message_text' => $fullText,
                    'from_me'      => true,
                    'is_read'      => true,
                    'sent_at'      => now(),
                ]);

                $this->newMessage = '';
                $this->loadMessages();
                $this->loadConversations();
                $this->dispatchBrowserEvent('scrollToBottom');
            } else {
//                 Log::error('Messenger send failed: ' . $response->body());
                session()->flash('error', 'Failed to send: ' . ($response->json('error.message') ?? 'Meta API error'));
            }
        } catch (\Exception $e) {
//             Log::error('Messenger send error: ' . $e->getMessage());
            session()->flash('error', 'Could not reach Meta API.');
        }
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

        return view('livewire.messenger.chat', [
            'displayConversations' => array_values($conversations),
            'activeConv'           => $activeConv,
        ]);
    }
}
