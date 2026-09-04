<?php

namespace App\Http\Livewire\Dialer\Settings\Campaign;

use App\Models\Campaign;
use App\Models\CampaignAgentDialLimit;
use App\Models\User;
use Livewire\Component;

class MaxDialCount extends Component
{
    protected $listeners = [
        'showMaxDialCountModal' => 'openModal',
    ];

    public $modalOpen = false;
    public $campaignId = null;
    public $campaignName = '';

    public $agents = [];
    public $selectedAgentId = null;
    public $maxCount = null;

    public $entries = [];

    public function openModal($campaignId)
    {
        $this->reset(['selectedAgentId', 'maxCount']);
        $this->campaignId = $campaignId;

        $campaign = Campaign::find($campaignId);
        $this->campaignName = $campaign ? $campaign->name : '';

        $this->loadAgents();
        $this->loadEntries();
        $this->modalOpen = true;
    }

    public function loadAgents()
    {
        $campaign = Campaign::find($this->campaignId);
        $userIds = $campaign && $campaign->assigned_users
            ? array_filter(array_map('intval', explode(',', trim($campaign->assigned_users))))
            : [];

        $this->agents = $userIds
            ? User::whereIn('id', $userIds)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];
    }

    public function loadEntries()
    {
        $this->entries = CampaignAgentDialLimit::where('campaign_id', $this->campaignId)
            ->with('agent:id,name')
            ->get();
    }

    public function updatedSelectedAgentId($value)
    {
        $this->maxCount = null;

        if (!$value) {
            return;
        }

        $entry = CampaignAgentDialLimit::where('campaign_id', $this->campaignId)
            ->where('agent_id', $value)
            ->first();

        if ($entry) {
            $this->maxCount = $entry->max_count;
        }
    }

    public function save()
    {
        $this->validate([
            'selectedAgentId' => 'required',
            'maxCount' => 'nullable|integer|min:0',
        ]);

        CampaignAgentDialLimit::updateOrCreate(
            ['campaign_id' => $this->campaignId, 'agent_id' => $this->selectedAgentId],
            ['max_count' => $this->maxCount !== '' ? $this->maxCount : null]
        );

        $this->loadEntries();
        $this->dispatchBrowserEvent('notify', 'Max dial count saved successfully!');
    }

    public function deleteEntry($id)
    {
        CampaignAgentDialLimit::where('id', $id)
            ->where('campaign_id', $this->campaignId)
            ->delete();

        $this->loadEntries();

        if ($this->selectedAgentId) {
            $this->maxCount = CampaignAgentDialLimit::where('campaign_id', $this->campaignId)
                ->where('agent_id', $this->selectedAgentId)
                ->value('max_count');
        }
    }

    public function render()
    {
        return view('livewire.dialer.settings.campaign.max-dial-count');
    }
}
