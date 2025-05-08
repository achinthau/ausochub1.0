<?php

namespace App\Http\Livewire\Chat;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class ChatNavButton extends Component
{
    public $messagesCount = 0;

    public function mount()
    {
        $this->loadMessagesCount();
    }

    public function loadMessagesCount()
    {
        $loggedUserId = Auth::id();
        $redisKey = "highlighted_users:$loggedUserId";

        $redis = Redis::connection();
        $redis->select(5);

        $messagesCountIds = $redis->get($redisKey);
        $messagesCountIds = $messagesCountIds ? json_decode($messagesCountIds, true) : [];

        $this->messagesCount = max(count($messagesCountIds) - 1, 0);
    }

    public function render()
    {
        return view('livewire.chat.chat-nav-button');
    }
}
