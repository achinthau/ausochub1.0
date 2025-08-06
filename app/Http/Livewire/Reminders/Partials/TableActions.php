<?php

namespace App\Http\Livewire\Reminders\Partials;

use App\Models\CallbackCustomer;
use Livewire\Component;

class TableActions extends Component
{
    public $callback;
    public $callback_at;
    public $isEdit = false;

    public function mount($id)
    {
        $this->callback = CallbackCustomer::find($id);
        $this->callback_at =$this->callback->callback_at;
    }

    public function setEdit()
    {
        $this->isEdit = true;
    }

    public function updateReminder()
    {
        

        $this->callback->callback_at = $this->callback_at;
        $this->callback->save();
        $this->isEdit = false;
        $this->emit('reminderTimeUpdated');

        session()->flash('success', 'updated successfully');
    }
    public function render()
    {
        return view('livewire.reminders.partials.table-actions');
    }
}
