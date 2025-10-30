<?php

namespace App\Http\Livewire\Reports\Partials;

use App\Models\Campaign;
use App\Models\Company;
use Livewire\Component;

class DialerAttemptCampaigns extends Component
{
    public $campaigns;
    public $selectedCampaign;

    public function mount()
    {
        $user = auth()->user();
        $userContexts = array_map('trim',explode(',', $user->tenant_context));
        $contextIds = Company::whereIn('name',$userContexts)->pluck('id');
        // dd($contextIds);
        $this->campaigns=Campaign::whereIn('company', $contextIds)->get();
        // dd($this->campaigns);
    }

    public function updatedSelectedCampaign($value)
    {
        $this->emit('dialerCampUpdated', $value); 
    }
    public function render()
    {
        return view('livewire.reports.partials.dialer-attempt-campaigns');
    }
}
