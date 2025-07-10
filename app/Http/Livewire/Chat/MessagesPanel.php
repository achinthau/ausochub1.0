<?php

namespace App\Http\Livewire\Chat;

use App\Jobs\SaveChatMessageJob;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class MessagesPanel extends Component
{
    public $receiver;
    public $messages = [];
    // protected $listeners = ['userSelected' => 'userSelected', 'saveData' => 'saveData'];   
    protected $listeners = ['userSelected' => 'userSelected'];

    public function userSelected(User $user)
    {
        if (!$user) return;

        $this->receiver = $user;
        $sender = Auth::id();

        $this->retriveCache($sender, $this->receiver->id);

        $this->dispatchBrowserEvent('scrollToBottom');
    }

    // public function saveData($sender, $receiver, $message)
    // {
    //     // dd($receiver);
    //     $redis = Redis::connection();
    //     $redis->select(5); // Switch to database 5

    //     $chatKey = "chat:{$sender}:{$receiver}"; // Unique chat key

    //     $messageData = [
    //         'sender' => $sender,
    //         'receiver' => $receiver,
    //         'text' => $message,
    //         'timestamp' => now()->timestamp
    //     ];

    //     $lastMessage = Redis::lindex($chatKey, -1); // Get the last message in Redis

    //     if ($lastMessage) {
    //         $lastMessage = json_decode($lastMessage, true);

    //         if ($lastMessage['text'] === $message && $lastMessage['sender'] === $sender) {
    //             return; // Prevent duplicate insertion
    //         }
    //     }

    //     $redis->rpush($chatKey, json_encode($messageData));

    //     $this->retriveCache($sender, $receiver);  // Get data from redis

    //     // Dispatch the queue job to save in MySQL
    //     SaveChatMessageJob::dispatch($sender, $receiver, $message, now()->timestamp);

    // }

    public function retriveCache($sender, $receiver)
    {
        $redis = Redis::connection(); // Get Redis connection
        $redis->select(5); // Switch to database 5

        $chatKey1 = "chat:{$sender}:{$receiver}";
        $chatKey2 = "chat:{$receiver}:{$sender}";

        $messages1 = $redis->lrange($chatKey1, -50, -1);
        $messages2 = $redis->lrange($chatKey2, -50, -1);

        if (empty($messages1) && empty($messages2)) {
            // 🔹 If Redis is empty, fetch from MySQL
            $messagesFromDB = ChatMessage::where(function ($query) use ($sender, $receiver) {
                $query->where('sender', $sender)->where('receiver', $receiver);
            })
                ->orWhere(function ($query) use ($sender, $receiver) {
                    $query->where('sender', $receiver)->where('receiver', $sender);
                })
                ->orderBy('timestamp', 'asc') // Ensure order by timestamp
                ->limit(50)
                ->get()
                ->toArray();

            // Store messages in Redis 
            // foreach ($messagesFromDB as $message) {
            //     $messageJson = json_encode($message);
            //     $redis->rpush($chatKey1, $messageJson);
            // }

            $this->messages = $messagesFromDB;

            // dd($this->messages);
            return;
        }


        // Decode JSON messages
        $messages1 = array_map(fn($msg) => json_decode($msg, true), $messages1);
        $messages2 = array_map(fn($msg) => json_decode($msg, true), $messages2);

        // Merge and sort by timestamp
        $mergedMessages = array_merge($messages1, $messages2);
        usort($mergedMessages, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

        $this->messages = $mergedMessages;

        // dd($this->messages);
    }


    public function getOlderMessages($sender, $receiver)
    {
        // $redis = Redis::connection();
        // $redis->select(5);

        // $chatKey1 = "chat:{$sender}:{$receiver}";
        // $chatKey2 = "chat:{$receiver}:{$sender}";

        // Get the oldest message's timestamp from current messages
        $oldestMessage = $this->messages[0] ?? null;
        // dd($oldestMessage);
        $lastTimestamp = $oldestMessage['timestamp'] ?? now()->timestamp; // Default to now if empty
        // dd($lastTimestamp);

        // Fetch the previous 50 messages older than the last timestamp
        $olderMessages = ChatMessage::where(function ($query) use ($sender, $receiver, $lastTimestamp) {
            $query->where('sender', $sender)
                ->where('receiver', $receiver)
                ->where('timestamp', '<', $lastTimestamp);
        })
            ->orWhere(function ($query) use ($sender, $receiver, $lastTimestamp) {
                $query->where('sender', $receiver)
                    ->where('receiver', $sender)
                    ->where('timestamp', '<', $lastTimestamp);
            })
            ->orderBy('timestamp', 'desc') // Get older messages first
            ->limit(50)
            ->get()
            ->toArray();

        // dd($olderMessages);

        if (!empty($olderMessages)) {
            // Reverse to maintain correct order (since fetched in DESC)
            $olderMessages = array_reverse($olderMessages);

            // Remove duplicates: Only keep messages not already in $this->messages
            $existingMessageIds = array_column($this->messages, 'id'); // Store current message IDs
            $uniqueMessages = array_filter($olderMessages, function ($msg) use ($existingMessageIds) {
                return !in_array($msg['id'], $existingMessageIds); // Keep only new messages
            });

            // Store unique messages in Redis
            // foreach ($uniqueMessages as $message) {
            //     $redis->lpush($chatKey1, json_encode($message)); // Push to Redis front
            // }

            // Append only new messages to the chat
            $this->messages = array_merge($uniqueMessages, $this->messages);
        }
    }




    public function render()
    {
        return view('livewire.chat.messages-panel');
    }
}
