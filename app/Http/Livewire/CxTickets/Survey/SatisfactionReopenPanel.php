<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CallbackCustomer;
use App\Models\CampaignAgentDialLimit;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactValid;
use App\Models\FeedContactValidReport;
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

    protected function copyCompletedToReport(FeedContactValid $feed): void
    {
        $completedStatuses = [1, 41, 42, 222];
        $status = (int) $feed->status;
        if (!in_array($status, $completedStatuses, true)) {
            return;
        }

        $attributes = [];
        foreach ($feed->getAttributes() as $attribute => $value) {
            if (in_array($attribute, (new FeedContactValidReport())->getFillable(), true)) {
                $attributes[$attribute] = $value;
            }
        }

        $attributes['feed_contact_id'] = $feed->id;

        FeedContactValidReport::updateOrCreate(
            ['feed_contact_id' => $feed->id],
            $attributes
        );

        $feed->copied_at = now();
        $feed->save();
    }

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

    protected function selectedSkipReasonTypes(): array
    {
        if (empty($this->selectedReasons)) {
            return [];
        }

        return DialerCallStatusOption::where('campaign_id', $this->campaignId)
            ->whereIn('type', [2, 3])
            ->whereIn('option', $this->selectedReasons)
            ->pluck('type')
            ->map(fn ($type) => (string) $type)
            ->unique()
            ->values()
            ->toArray();
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
            $this->feed->call_status_option_id = null;
            $this->feed->call_status_option_type = 'reopen';
            $this->feed->rate = null;
            $this->feed->comments = $this->comment;
            $this->feed->campaign_id = $this->campaignId;
            $this->feed->updated_by = Auth::id();
            $this->feed->attempted_at = now();
            $this->feed->status = 1;
            $this->feed->save();
            CampaignAgentDialLimit::incrementForFeed((int) $this->feed->feed_id, (int) Auth::id());
            $this->emit('FeedCompleted');
        } elseif ($this->isReOpen === 'skip') {
            $currentStatus = $this->feed->status;
            $currentAttempt = $this->feed->attempt_count;

            if (is_null($currentStatus)) {
                $this->feed->attempt_count = 1;
            } elseif ((string) $currentStatus === '2') {
                $this->feed->attempt_count = 2;
            } elseif ((string) $currentStatus === '22') {
                $this->feed->attempt_count = 3;
            } elseif ((string) $currentStatus === '3') {
                if (is_null($currentAttempt)) {
                    $this->feed->attempt_count = 1;
                } elseif ((int) $currentAttempt === 1) {
                    $this->feed->attempt_count = 2;
                } elseif ((int) $currentAttempt === 2) {
                    $this->feed->attempt_count = 3;
                }
            }

            $this->feed->call_status_option_id = implode(',', array_values(array_unique($this->selectedReasons)));
            $this->feed->call_status_option_type = implode(',', $this->selectedSkipReasonTypes());
            $this->feed->rate = null;
            $this->feed->comments = $this->comment;
            $this->feed->campaign_id = $this->campaignId;
            $this->feed->updated_by = Auth::id();
            $this->feed->attempted_at = now();

            if ((string) $currentStatus === '3' && $currentAttempt !== null && (int) $currentAttempt === 2) {
                $this->feed->status = 222;
            } elseif ((string) $this->feed->status === '222') {
                // keep as 222
            } else {
                $this->feed->status = 3;
            }

            $this->feed->save();

            if ((string) $this->feed->status === '222') {
                $this->copyCompletedToReport($this->feed);
            }
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

        $this->feed->call_status_option_id = null;
        $this->feed->call_status_option_type = 'remind';
        $this->feed->rate = null;
        $this->feed->comments = $this->callbackComment;
        $this->feed->campaign_id = $this->campaignId;
        $this->feed->updated_by = Auth::id();
        $this->feed->attempted_at = now();
        $this->feed->save();

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
