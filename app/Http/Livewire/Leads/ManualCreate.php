<?php

namespace App\Http\Livewire\Leads;

use App\Models\Lead;
use App\Models\QueueCount;
use App\Models\Skill;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;

class ManualCreate extends Component
{
    use Actions;
    public Lead $lead;

    public $showCreateLeadModal = false;

    public $skills;

    protected $rules = [
        'lead.contact_number' => 'required|size:9',
        'lead.skill_id' => 'required',
        'lead.first_name' => 'required',
        'lead.last_name' => 'nullable',
        'lead.nic' => 'nullable',
        'lead.address_line_1' => 'nullable',
        'lead.address_line_2' => 'nullable',
        'lead.city' => 'nullable',
        'lead.contact_number_2' => 'nullable',
        'lead.email' => 'nullable|email',
        //'lead.priority_level_id'=>'required',
        //'lead.satisfaction_level_id'=>'required',

    ];

    public function mount()
    {
        $this->skills = Skill::all();
        $this->lead = new Lead();
    }

    public function render()
    {
        return view('livewire.leads.manual-create');
    }

    public function updatedShowCreateLeadModal($value)
    {
    }

    public function save()
    {
        $this->validate();
        $callLogs = QueueCount::where('ani', $this->lead->contact_number)->orderBy('date', 'DESC')->first();
        if ($callLogs) {
            $this->lead->unique_id = $callLogs->uniqueid;
        } else {
            $this->lead->unique_id = "N/A";
        }
        $this->lead->status_id = 2;
        $this->lead->agent_id = Auth::id();
        $this->lead->extension = Auth::user()->extension ?? "000";
        $this->lead->save();
        $this->showCreateLeadModal = false;
        $this->notification()->success(
            $title = 'Success',
            $description = 'Customer successfull saved'
        );

        redirect(route('leads.show', ['lead' => $this->lead]));
    }
}
