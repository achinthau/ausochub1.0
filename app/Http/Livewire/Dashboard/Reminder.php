<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class Reminder extends Component
{

 public $isReminder = false;

public function mount()
{
    $this->updateReminderStatus();
}

// This is what wire:poll will re-trigger
public function updateReminderStatus()
{
    Redis::connection()->select(6);
    $data = json_decode(Redis::get('isReminder:' . Auth::id()), true);
    $this->isReminder = $data['isReminder'] ?? false;
}

public function showNotifications()
{
    Redis::connection()->select(6);
Redis::del('isReminder:' . auth()->id());
}
    public function render()
    {
        return view('livewire.dashboard.reminder');
    }
}
