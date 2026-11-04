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
    public $editStatus = null;

    public $newOption = '';
    public $newType = 1;
    public $newStatus = null;

    public function openModal($campaignId)
    {
        $this->reset(['editingId', 'editOption', 'editType', 'editStatus', 'newOption', 'newType', 'newStatus']);
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
            'newType' => 'required|in:1,2,3,4,41,42',
            'newStatus' => 'nullable|string|max:10',
        ]);

        DialerCallStatusOption::create([
            'option' => $this->newOption,
            'type' => $this->newType,
            'status' => $this->statusOrDefault($this->newStatus, $this->newType),
            'campaign_id' => $this->campaignId,
        ]);

        $this->newOption = '';
        $this->newType = 1;
        $this->newStatus = null;
        $this->loadOptions();
    }

    public function startEdit($id)
    {
        $option = DialerCallStatusOption::find($id);
        if ($option) {
            $this->editingId = $id;
            $this->editOption = $option->option;
            $this->editType = $option->type;
            $this->editStatus = $option->status;
        }
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'editOption', 'editType', 'editStatus']);
    }

    public function saveEdit()
    {
        $this->validate([
            'editOption' => 'required|string|max:255',
            'editType' => 'required|in:1,2,3,4,41,42',
            'editStatus' => 'nullable|string|max:10',
        ]);

        $option = DialerCallStatusOption::find($this->editingId);
        if ($option) {
            $option->update([
                'option' => $this->editOption,
                'type' => $this->editType,
                'status' => $this->statusOrDefault($this->editStatus, $this->editType),
            ]);
        }

        $this->reset(['editingId', 'editOption', 'editType', 'editStatus']);
        $this->loadOptions();
    }

    /**
     * Fall back to the type-based status when no explicit status is given.
     */
    protected function statusOrDefault($status, $type): string
    {
        if ($status !== null && trim((string) $status) !== '') {
            return trim((string) $status);
        }

        return (int) $type === 4 ? '2' : (string) $type;
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
