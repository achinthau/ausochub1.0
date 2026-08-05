<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CallbackCustomer;
use App\Models\CampaignAgentDialLimit;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
use App\Models\FeedContactValid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SatisfactionReopenPanel extends Component
{
    public $feedContactId;
    public $campaignId;
    public $isReOpen = '';
    public $cxTicketReOpenModal = false;
    public $comment = '';

    public $callBack = false;
    public $callbackDate;
    public $callbackTime;
    public $callbackComment;
    public array $skipReasons = [];
    public array $selectedReasons = [];
    public $selectedSkippingReason = null;
    public $feed;

    protected $listeners = ['showReOpenPanel' => 'showReOpenModal'];

    protected $rules = [
        'comment' => 'required|string|min:5',
    ];

    public function render()
    {
        return view('livewire.cx-tickets.survey.satisfaction-reopen-panel');
    }

    public function updatedselectedSkippingReason($value)
    {
        if ($value) {
            $this->selectReason($value);
        }
    }

    public function selectReason($reason)
    {
        if (!in_array($reason, $this->selectedReasons)) {
            $this->selectedReasons[] = $reason;
        }
    }

    public function removeReason($reason)
    {
        $this->selectedReasons = array_filter($this->selectedReasons, fn($r) => $r !== $reason);
    }

    protected function loadSkipReasons()
    {
        $this->skipReasons = DialerCallStatusOption::where('campaign_id', $this->campaignId)
            ->whereIn('type', [2, 3])
            ->pluck('option')
            ->values()
            ->toArray();
    }

    protected function selectedSkipReasonIds(): array
    {
        $ids = [];
        $options = DialerCallStatusOption::where('campaign_id', $this->campaignId)
            ->whereIn('type', [2, 3])
            ->get();
        $byName = $options->pluck('id', 'option');

        foreach ($this->selectedReasons as $reason) {
            if (isset($byName[$reason])) {
                $ids[] = $byName[$reason];
            }
        }
        return $ids;
    }

    public function showReOpenModal($feedContactId, $value, $campaignId)
    {
        $this->feedContactId = $feedContactId;
        $this->campaignId = $campaignId;
        $this->isReOpen = $value;        // value: 'reopen', 'skip', 'remind'
        $this->cxTicketReOpenModal = true;
        $this->callBack = $value === 'remind';
        $this->feed = $feedContactId ? FeedContactValid::find($feedContactId) : null;
        $this->loadSkipReasons();
    }

    public function reOpenTicket()
    {
        $this->validate();

        if (!$this->feed) {
            $this->cxTicketReOpenModal = false;
            return;
        }

        if ($this->isReOpen === 'reopen') {
            FeedContactAttempt::create([
                'feed_contact_valid_id' => $this->feed->id,
                'call_status_option_id' => null,
                'call_status_option_type' => 'reopen',
                'rate' => null,
                'comments' => $this->comment,
                'campaign_id' => $this->campaignId,
                'updated_by' => Auth::id(),
            ]);

            $this->feed->status = 1;
            $this->feed->save();
            CampaignAgentDialLimit::incrementForFeed((int) $this->feed->feed_id, (int) Auth::id());
            $this->emit('FeedCompleted');
        } elseif ($this->isReOpen === 'skip') {
            $ids = $this->selectedSkipReasonIds();

            FeedContactAttempt::create([
                'feed_contact_valid_id' => $this->feed->id,
                'call_status_option_id' => implode(',', $ids),
                'call_status_option_type' => implode(',', array_values(array_unique(array_map(fn($id) => (string) DialerCallStatusOption::where('id', $id)->value('type'), $ids)))),
                'rate' => null,
                'comments' => $this->comment,
                'campaign_id' => $this->campaignId,
                'updated_by' => Auth::id(),
            ]);

            $this->feed->status = 3;
            $this->feed->save();
        }

        $this->selectedReasons = [];

        $this->emit('cxTicketSurveyUpdated');
        $this->emit('FeedCompleted');
        $this->cxTicketReOpenModal = false;
        $this->reset(['comment', 'feedContactId', 'isReOpen', 'callBack']);
    }

    public function saveCallback()
    {
        if (!$this->feed) {
            $this->cxTicketReOpenModal = false;
            return;
        }

        $this->validate([
            'callbackDate' => 'required|date',
            'callbackTime' => 'required',
            'callbackComment' => 'nullable|string',
        ]);

        FeedContactAttempt::create([
            'feed_contact_valid_id' => $this->feed->id,
            'call_status_option_id' => null,
            'call_status_option_type' => 'remind',
            'rate' => null,
            'comments' => $this->callbackComment,
            'campaign_id' => $this->campaignId,
            'updated_by' => Auth::id(),
        ]);

        CallbackCustomer::create([
            'agent_id' => auth()->id(),
            'contact_number' => $this->feed->contact_no_01 ?? $this->feed->contact_no_02,
            'src' => 'survey',
            'callback_at' => Carbon::parse("{$this->callbackDate} {$this->callbackTime}"),
            'comment' => $this->callbackComment,
        ]);

        session()->flash('messagedialog', 'Callback saved successfully.');

        $this->cxTicketReOpenModal = false;
        $this->emit('cxTicketSurveyUpdated');
        $this->reset(['callBack', 'callbackDate', 'callbackTime', 'callbackComment', 'comment', 'isReOpen', 'feedContactId']);
    }
}
