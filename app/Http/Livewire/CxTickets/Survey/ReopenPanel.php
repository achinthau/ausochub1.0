<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CallbackCustomer;
use App\Models\FeedContactValid;
use App\Models\FeedContactValidReport;
use App\Models\CampaignAgentDialLimit;
use Livewire\Component;
use App\Models\CxTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReopenPanel extends Component
{
    public $ticket_id;
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
        return view('livewire.cx-tickets.survey.reopen-panel');
    }

    public function mount()
    {
        $this->skipReasons = [
        '1st Call No Answer',
        '2nd Call No Answer',
        '3rd Call No Answer',
        'Not in use',
        'Unreacherble'
    ];
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

    public function showReOpenModal($id, $value, $validContact=null, $feedContactId=null)
    {
        $this->ticket_id = $id;
        $this->isReOpen = $value;        // value: 'reopen', 'skip', 'remind'
        $this->cxTicketReOpenModal = true;
        $this->callBack = $value === 'remind';
        if($validContact || $feedContactId)
        {
            // $this->feed = FeedContactValid::find($validContact);
            $this->feed = $feedContactId
                ? FeedContactValid::find($feedContactId)
                : FeedContactValid::whereRaw('TRIM(priority_field) = ?', [trim($validContact)])->first();
        }
    }

    public function reOpenTicket()
    {
        $this->validate();

        $ticket = CxTicket::find($this->ticket_id);
        if ($ticket) {
            if ($this->isReOpen === 'reopen') {
                $ticket->status = 'ReOpened';
                $ticket->reopened_reasons = $this->comment;
                $ticket->reopened_by = Auth::user()->name;

                if($this->feed)
        {
            $this->feed->status = 1;
            $this->feed->save();
            CampaignAgentDialLimit::incrementForFeed((int) $this->feed->feed_id, (int) Auth::id());
            $this->emit('FeedCompleted');
        }
            }
            elseif ($this->isReOpen === 'skip') {
                $allReasons = array_filter(array_merge($this->selectedReasons, [$this->comment]));
                $ticket->status = 'Skip';
                $ticket->skipped_reasons = implode(', ', $allReasons);
                $ticket->skipped_by = Auth::user()->name;

                if ($this->feed) {
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
                            $this->feed->status = 222;
                            $this->feed->call_status_option_id = null;
                            $this->feed->call_status_option_type = 'skip';
                            $this->feed->comments = $this->comment;
                            $this->feed->updated_by = Auth::id();
                            $this->feed->attempted_at = now();
                            $this->feed->save();
                            $this->copyCompletedToReport($this->feed);

                            $completedStatuses = [1, 41, 42, 222];
                            if (in_array((int) $this->feed->status, $completedStatuses, true)) {
                                $attributes = [];
                                foreach ($this->feed->getAttributes() as $attribute => $value) {
                                    if (in_array($attribute, (new \App\Models\FeedContactValidReport())->getFillable(), true)) {
                                        $attributes[$attribute] = $value;
                                    }
                                }
                                $attributes['feed_contact_id'] = $this->feed->id;
                                \App\Models\FeedContactValidReport::updateOrCreate(
                                    ['feed_contact_id' => $this->feed->id],
                                    $attributes
                                );
                                $this->feed->copied_at = now();
                                $this->feed->save();
                            }
                        } else {
                            $this->feed->call_status_option_id = null;
                            $this->feed->call_status_option_type = 'skip';
                            $this->feed->comments = $this->comment;
                            $this->feed->updated_by = Auth::id();
                            $this->feed->attempted_at = now();
                            $this->feed->save();
                        }
                    }

                    if ((string) $this->feed->status === '222') {
                        $this->feed->call_status_option_id = null;
                        $this->feed->call_status_option_type = 'skip';
                        $this->feed->comments = $this->comment;
                        $this->feed->updated_by = Auth::id();
                        $this->feed->attempted_at = now();
                        $this->feed->save();
                        $this->copyCompletedToReport($this->feed);
                    } else {
                        $this->feed->status = 3;
                        $this->feed->call_status_option_id = null;
                        $this->feed->call_status_option_type = 'skip';
                        $this->feed->comments = $this->comment;
                        $this->feed->updated_by = Auth::id();
                        $this->feed->attempted_at = now();
                        $this->feed->save();
                    }
                }
            }
            $ticket->save();
        }

        $this->selectedReasons = [];

        $this->emit('cxTicketSurveyUpdated');
        $this->emit('FeedCompleted');
        $this->cxTicketReOpenModal = false;
        $this->reset(['comment', 'ticket_id', 'isReOpen', 'callBack']);
        
    }

    public function saveCallback()
    {
        $ticket = CxTicket::find($this->ticket_id);
        if ($ticket){
            $ticket->status = 'Remind';
        }
        $ticket->save();

        $this->validate([
            'callbackDate' => 'required|date',
            'callbackTime' => 'required',
            'callbackComment' => 'nullable|string',
        ]);

        CallbackCustomer::create([
            'agent_id' => auth()->id(),
            'cx_ticket_id' => $this->ticket_id,
            'contact_number' => $ticket->customer_contact_01,
            'src' => 'survey',
            'callback_at' => Carbon::parse("{$this->callbackDate} {$this->callbackTime}"),
            'comment' => $this->callbackComment,
        ]);

        session()->flash('messagedialog', 'Callback saved successfully.');

        $this->cxTicketReOpenModal = false;
        $this->emit('cxTicketSurveyUpdated');
        $this->reset(['callBack', 'callbackDate', 'callbackTime', 'callbackComment', 'comment', 'isReOpen', 'ticket_id']);
    }
}
