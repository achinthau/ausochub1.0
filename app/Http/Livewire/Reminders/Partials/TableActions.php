<?php

namespace App\Http\Livewire\Reminders\Partials;

use App\Models\CallbackCustomer;
use App\Models\User;
use Livewire\Component;

class TableActions extends Component
{
    public $callback;
    public $callback_at;
    public $isEdit = false;
    public $users;
    public $showDropdown = false;
    public $selectedUser;

    public function mount($id)
    {
        $this->callback = CallbackCustomer::find($id);
        $this->callback_at =$this->callback->callback_at;
        $this->users = User::all();
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

     public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function assign()
    {
        // CallbackCustomer::where('id', $this->callbackId)->update([
        //     'agent_id' => $this->selectedUser
        // ]);
        // dd($this->callback);
        $this->callback->agent_id = $this->selectedUser;
        $this->callback->save();

        session()->flash('success', 'User assigned successfully.');
        $this->showDropdown = false;
        $this->emit('reminderTimeUpdated');
    }
    public function render()
    {
        return view('livewire.reminders.partials.table-actions');
    }
}
