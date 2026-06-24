<?php

namespace App\Http\Livewire\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappMetaMessage;

class WhatsappNavButton extends Component
{
    public $unreadCount = 0;
    public $whatsappServiceUrl;

    protected $listeners = [
        'whatsappMessageReceived' => 'incrementCount',
        'whatsappRefreshUnread' => 'loadUnreadCount'
    ];

    public function mount()
    {
        $this->whatsappServiceUrl = config('services.whatsapp.webjs_url', 'http://localhost:3001');
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        $type = config('services.whatsapp.type', 'webjs');

        if ($type === 'meta_api') {
            try {
                $this->unreadCount = WhatsappMetaMessage::unread()->count();
            } catch (\Exception $e) {
                $this->unreadCount = 0;
            }
            return;
        }

        // WebJS unread count fetch
        try {
            $response = Http::timeout(5)->get($this->whatsappServiceUrl . '/unread-count');
            if ($response->successful()) {
                $this->unreadCount = $response->json()['count'] ?? 0;
            }
        } catch (\Exception $e) {
            // Silently fail for navbar
            $this->unreadCount = 0;
        }
    }

    public function incrementCount()
    {
        // Simply reload to be accurate
        $this->loadUnreadCount();
    }

    public function render()
    {
        return view('livewire.whatsapp.whatsapp-nav-button');
    }
}
