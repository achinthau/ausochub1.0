<?php

namespace App\Http\Livewire\Chat;

use App\Jobs\SaveChatMessageJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class Messages extends Component
{
    protected $listeners = ['saveData' => 'saveData', 'highlightUser' => 'highlightUsers'];

    public function saveData($sender, $receiver, $message)
{
    // dd($receiver);
    $redis = Redis::connection();
    $redis->select(5); // Switch to database 5

    $chatKey = "chat:{$sender}:{$receiver}"; // Unique chat key

    $timestamp = now()->timestamp;

    $messageData = [
        'sender' => $sender,
        'receiver' => $receiver,
        'text' => $message,
        'timestamp' => $timestamp
    ];

    $lastMessage = Redis::lindex($chatKey, -1); // Get the last message in Redis

    if ($lastMessage) {
        $lastMessage = json_decode($lastMessage, true);

        if ($lastMessage['text'] === $message && $lastMessage['sender'] === $sender) {
            return; // Prevent duplicate insertion
        }
    }

    $redis->rpush($chatKey, json_encode($messageData));

    // $this->retriveCache($sender, $receiver);  // Get data from redis

    // Dispatch the queue job to save in MySQL
    SaveChatMessageJob::dispatch($sender, $receiver, $message, $timestamp);






    $redisKey = "highlighted_users:$receiver";
    Redis::select(5);
    $storedUserIds = Redis::get($redisKey);
    $storedUserIds = $storedUserIds ? json_decode($storedUserIds, true) : [];

    $userIds = is_array($sender) ? $sender : [$sender];
    $storedUserIds = array_unique(array_merge($storedUserIds, $userIds));
    Redis::set($redisKey, json_encode($storedUserIds));

}

    public function render()
    {
        return view('livewire.chat.messages');
    }
}
