<?php

namespace App\Http\Livewire\Reminders\Partials;

use App\Models\CallbackCustomer;
use App\Models\CxTicket;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
        $this->callback_at = $this->callback->callback_at;
        $this->selectedUser = $this->callback->agent_id;
        // $this->users = User::where('tenant_context',auth()->user()->tenant_context)->get();
        $tenantContexts = explode(',', auth()->user()->tenant_context);

        $this->users = User::where(function ($query) use ($tenantContexts) {
            foreach ($tenantContexts as $context) {
                $query->orWhere('tenant_context', 'like', '%' . trim($context) . '%');
            }
        })->get();

    }

    public function callApi($id)
{
    $extension = Auth::user()->extension;
    $tenant_context = Auth::user()->tenant_context;
    $this->callback = CallbackCustomer::find($id);
    $phone = $this->callback->contact_number;
    if($this->callback->cx_ticket_id)
    {
        $ticket = CxTicket::find($this->callback->cx_ticket_id);
        if($ticket)
        {
            $tenant_context = $ticket->company;
        }
    }
    // elseif($this->callback->lead_id)
    // {
    //     $lead = Lead::find($this->callback->lead_id);
    //     if($lead)
    //     {
    //         $phone = $lead->contact_number;
    //     }
    // }
    
    $url = "123.231.74.22:8080/ausoadmin/dialscripts/dial.php";
    // $url = env('CALL_SERVER_API_URL') .'/dialscripts/dial.php';

    $response = $response = Http::get($url, [
        'type'   => 'out',
        'exten'  => $extension,
        'num'    => $phone,
        'tenant' => $tenant_context,
    ]);

    if ($response->successful()) {
        $this->dispatchBrowserEvent('notify', ['message' => 'API call successful!']);
    } else {
        $this->dispatchBrowserEvent('notify', ['message' => 'API call failed!']);
    }
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
