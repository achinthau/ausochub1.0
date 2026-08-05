<?php

namespace App\Http\Livewire\Dialer\Settings\Campaign;

use App\Models\Campaign;
use App\Models\DialerCallStatusOption;
use Livewire\Component;

class StatusOption extends Component
{
    protected $listeners = [
        'showStatusOptionModal' => 'openModal',
    ];

    public $modalOpen = false;
    public $campaignId = null;
    public $campaignName = '';
    public $campaignType = null;

    public $options = [];

    public $editingId = null;
    public $editOption = '';
    public $editType = 1;

    public $newOption = '';
    public $newType = 1;

    public function openModal($campaignId)
    {
        $this->reset(['editingId', 'editOption', 'editType', 'newOption', 'newType']);
        $this->campaignId = $campaignId;
        $campaign = Campaign::find($campaignId);
        $this->campaignName = $campaign ? $campaign->name : '';
        $this->campaignType = $campaign ? $campaign->service_type : null;
        $this->loadOptions();
        $this->modalOpen = true;
    }

    public function loadOptions()
    {
        $this->options = DialerCallStatusOption::where('campaign_id', $this->campaignId)
            ->orderBy('type')
            ->orderBy('option')
            ->get();
    }

    public function addOption()
    {
        $this->validate([
            'newOption' => 'required|string|max:255',
            'newType' => 'required|in:1,2,3,4',
        ]);

        DialerCallStatusOption::create([
            'option' => $this->newOption,
            'type' => $this->newType,
            'campaign_id' => $this->campaignId,
        ]);

        $this->newOption = '';
        $this->newType = 1;
        $this->loadOptions();
    }

    public function startEdit($id)
    {
        $option = DialerCallStatusOption::find($id);
        if ($option) {
            $this->editingId = $id;
            $this->editOption = $option->option;
            $this->editType = $option->type;
        }
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'editOption', 'editType']);
    }

    public function saveEdit()
    {
        $this->validate([
            'editOption' => 'required|string|max:255',
            'editType' => 'required|in:1,2,3,4',
        ]);

        $option = DialerCallStatusOption::find($this->editingId);
        if ($option) {
            $option->update([
                'option' => $this->editOption,
                'type' => $this->editType,
            ]);
        }

        $this->reset(['editingId', 'editOption', 'editType']);
        $this->loadOptions();
    }

    public function deleteOption($id)
    {
        DialerCallStatusOption::where('id', $id)->where('campaign_id', $this->campaignId)->delete();
        $this->loadOptions();
    }

    public function render()
    {
        return view('livewire.dialer.settings.campaign.status-option');
    }
}
