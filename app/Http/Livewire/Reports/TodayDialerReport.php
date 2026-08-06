<?php

namespace App\Http\Livewire\Reports;

use App\Models\Campaign;
use App\Models\Company;
use Livewire\Component;

class TodayDialerReport extends Component
{
    public $campaigns = [];
    public $selectedCampaign = null;

    public function mount()
    {
        $user = auth()->user();

        $query = Campaign::query()->with('types')->orderBy('name');

        if ($user->can('is-admin')) {
            $userContexts = array_map('trim', explode(',', $user->tenant_context));
            $contextIds = Company::whereIn('name', $userContexts)->pluck('id');
            $query->whereIn('company', $contextIds);
        } else {
            $query->whereRaw('FIND_IN_SET(?, assigned_users)', [$user->id]);
        }

        $this->campaigns = $query->get();
    }

    public function updatedSelectedCampaign($value)
    {
        $this->emit('todayDialerCampaignSelected', $value ?: null);
    }

    public function render()
    {
        return view('livewire.reports.today-dialer-report');
    }
}
