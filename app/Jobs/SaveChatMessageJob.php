<?php

namespace App\Jobs;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveChatMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $sender;
    public $receiver;
    public $message;
    public $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sender, $receiver, $message, $timestamp)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->message = $message;
        $this->timestamp = $timestamp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ChatMessage::create([
            'sender' => $this->sender,
            'receiver' => $this->receiver,
            'text' => $this->message,
            'timestamp' => $this->timestamp,
        ]);
    }
}
