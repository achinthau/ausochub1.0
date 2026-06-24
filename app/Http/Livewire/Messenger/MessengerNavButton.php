<?php

namespace App\Http\Livewire\Messenger;

use App\Models\MessengerMessage;
use Livewire\Component;

class MessengerNavButton extends Component
{
    public int $unreadCount = 0;

    protected $listeners = [
        'messengerRefresh' => 'loadUnreadCount',
    ];

    public function mount(): void
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount(): void
    {
        try {
            $this->unreadCount = MessengerMessage::unread()->count();
        } catch (\Exception $e) {
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.messenger.messenger-nav-button');
    }
}
