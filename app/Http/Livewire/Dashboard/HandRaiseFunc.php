<?php

namespace App\Http\Livewire\Dashboard;
use Illuminate\Support\Facades\Redis;


use Livewire\Component;

class HandRaiseFunc extends Component
{
    protected $listeners = ['saveData' => 'saveData'];

    public function saveData($userId)
{
    // dd('h');

    $redis = Redis::connection();
    $redis->select(1);

    $ttl = env('RAISE_HAND_TIME', 10);
    $key = "hand_raised:$userId";
    $redis->setex($key, $ttl, true); // store for 10 seconds

    // // Optionally broadcast to others
    // $this->emit('handRaiseUpdated', $userId);
    
}

    public function render()
    {
        return view('livewire.dashboard.hand-raise-func');
    }
}
