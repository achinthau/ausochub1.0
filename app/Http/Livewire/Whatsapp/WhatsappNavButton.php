<?php

namespace App\Http\Livewire\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            $response = Http::timeout(5)->get($this->whatsappServiceUrl . '/unread-count');
            if ($response->successful()) {
                $this->unreadCount = $response->json()['count'] ?? 0;
            }
        } catch (\Exception $e) {
            // Silently fail for navbar
        }
    }

    public function incrementCount()
    {
        // Simply reload from service to be accurate
        $this->loadUnreadCount();
    }

    public function render()
    {
        return view('livewire.whatsapp.whatsapp-nav-button');
    }
}
