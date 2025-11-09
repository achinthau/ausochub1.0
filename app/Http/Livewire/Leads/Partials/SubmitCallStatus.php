<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\Campaign;
use App\Models\CxTicket;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
use App\Models\FeedContactValid;
use Hamcrest\Type\IsInteger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubmitCallStatus extends Component
{

    public $CallStatusModal = false;
    public $feed;
    public $status;

    public $options = [];
    public $selectedOption = null;
    public $comment = '';

    public $applyToAll = false;
    public $feedCount = 0;
    public $paymentDate;
    public $cxTicketId;
    public $campaign;

    protected $listeners = ['openCallStatusModal' => 'openModal'];

    public function openModal($id, $status, $feedCount, $ticketId = null)
    {
        // dd(is_numeric($id));
        $this->CallStatusModal = true;
        $this->feedCount = $feedCount;
        // $this->feed = FeedContactValid::find($id);
        if (is_numeric($id)) {
            $this->feed = FeedContactValid::find($id);
        } else {
            $this->feed = FeedContactValid::where('priority_field', $id)->first();
        }
        // dd($this->feedCount);
        // dd($this->feed->id);
        // $this->campaign = Campaign::whereIn('assigned_feeds', [$this->feed->feed_id])->get();
        $feedId = $this->feed->feed_id;
        $this->campaign = Campaign::whereRaw('FIND_IN_SET(?, assigned_feeds)', [$feedId])->get();

        // dd($this->campaign);
        $this->campaign = $this->campaign->first()->id;
        // dd($this->campaign);
        $this->status = $status;
        // $this->options = DialerCallStatusOption::where('type', $status === 'answered' ? 1 : 2)
        //     ->pluck('option', 'id')
        //     ->toArray();
        $this->options = DialerCallStatusOption::where('type', $status === 'answered' ? 1 : 2)
            ->get();

        $this->selectedOption = null;
        $this->comment = '';
        $this->cxTicketId = $ticketId;
    }

    // protected $rules = [
    //     // 'comment' => 'required|string|min:5',
    //     'selectedOption' => 'required|exists:dialer_call_status_options,id',
    // ];

    protected $messages = [
        // 'comment.required' => 'Please select a payment date.',
        'paymentDate.required' => 'The payment date must be a valid date.',
        'paymentDate.after_or_equal' => 'The payment date cannot be in the past.',
        'selectedOption.required' => 'You must select a call status.',
    ];


    public function submit()
    {
        if (empty($this->selectedOption)) {
            $this->addError('selectedOption', 'Please select an option.');
            return;
        }

        // Decode the JSON from the dropdown
        $option = json_decode($this->selectedOption, true);

        // Validation based on the option value
        if ($option['value'] === 'Promised to pay') {
            $this->validate([
                'paymentDate' => 'required|date|after_or_equal:today',
                'selectedOption' => 'required',
            ]);

            // Append payment date to comment
            $this->comment = $this->comment . ' Payment Date: ' . $this->paymentDate;
        } else {
            $this->validate([
                'selectedOption' => 'required',
            ]);
        }

        if ($this->applyToAll) {
            // Get the contact number
            $phone = $this->feed->contact_no_01;
            $phone2 = $this->feed->contact_no_02;

            // Fetch only feeds with same number AND not updated before
            $feeds = FeedContactValid::where(function ($query) use ($phone, $phone2) {
                // if ($phone) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone)
                    // }
                    // if ($phone2) {
                    // $query->orWhere('contact_no_01', $phone2)
                    ->orWhere('contact_no_01', $phone2)
                    ->orWhere('contact_no_02', $phone2);
                // }
            })
                ->where(function ($query) {
                    $query->whereNull('status') // Fresh ones
                        ->orWhere('status', 3)
                        ->orWhere('status', "LIKE", '2%'); // No Answer retries
                })
                ->when($this->feed->feed_id, function ($query, $feedId) {
                    $query->where('feed_id', $feedId);
                })
                ->get();

            foreach ($feeds as $feed) {
                if ($this->status == 'answered') {
                    $feed->status = 1; // Answered
                } else {
                    // If first time (null or not 2-based)
                    // if ($feed->status == 2) {
                    //     $feed->status = 22; // Second time
                    // } elseif ($feed->status == 22) {
                    //     $feed->status = 222; // Third time
                    // } 
                    // // elseif ($feed->status == 222) {
                    // //     $feed->status = 4; // Third time
                    // // } 
                    // else {
                    //     $feed->status = 2; // First time "no answer"
                    // }
                    if (str_starts_with((string) $feed->status, '2')) {
                        $feed->status = (int) ($feed->status . '2');
                    } else {
                        $feed->status = 2; // First time "no answer"
                    }


                    // Set next date each time for status 2-based
                    $feed->next_available_at = now()->addDay();


                }

                $feed->save();

                FeedContactAttempt::create([
                    'feed_contact_valid_id' => $feed->id,
                    'call_status_option_id' => $option['id'],
                    'comments' => $this->comment,
                    'campaign_id' => $this->campaign,
                    'updated_by' => Auth::id(),
                    'call_status_option_type' =>$option['type']
                ]);
            }

            if ($this->cxTicketId) {
                $tickets = CxTicket::where(function ($query) use ($phone, $phone2) {
                    $query->where('customer_contact_01', $phone)
                        ->orWhere('customer_contact_02', $phone)
                        ->orWhere('customer_contact_01', $phone2)
                        ->orWhere('customer_contact_02', $phone2);
                })
                    ->where('status', 'Closed')
                    ->orWhere('status', 'Skip')
                    ->get();

                foreach ($tickets as $ticket) {
                    if ($ticket) {
                        $ticket->status = 'Skip';
                        if ($this->selectedOption) {
                            $optionName = DialerCallStatusOption::where('id', $this->selectedOption)->value('option');
                            $ticket->skipped_reasons = $optionName . ' ' . $this->comment;
                        }

                        $ticket->skipped_by = Auth::user()->name;
                    }
                    $ticket->save();
                }
            }


        } else {
            // ✅ Update only the current feed
            if ($this->status == 'answered') {
                $this->feed->status = 1; // Answered
            } else {
                // If first time (null or not 2-based)
                // if ($this->feed->status == 2) {
                //     $this->feed->status = 22; // Second time
                // } elseif ($this->feed->status == 22) {
                //     $this->feed->status = 222; // Third time
                // } 
                // // elseif ($this->feed->status == 222) {
                // //     $this->feed->status = 4; // Third time
                // // }
                //  else {
                //     $this->feed->status = 2; // First time "no answer"
                // }

                if (str_starts_with((string) $this->feed->status, '2')) {
                    $this->feed->status = (int) ($this->feed->status . '2');
                } else {
                    $this->feed->status = 2; // First time "no answer"
                }

                // Set next date each time for status 2-based
                $this->feed->next_available_at = now()->addDay();

                if ($this->cxTicketId) {
                    $ticket = CxTicket::find($this->cxTicketId);
                    if ($ticket) {
                        $ticket->status = 'Skip';
                        if ($this->selectedOption) {
                            $optionName = DialerCallStatusOption::where('id', $this->selectedOption)->value('option');
                            $ticket->skipped_reasons = $optionName . ' ' . $this->comment;
                        }

                        $ticket->skipped_by = Auth::user()->name;
                    }
                    $ticket->save();
                }
            }
            $this->feed->save();

            FeedContactAttempt::create([
                'feed_contact_valid_id' => $this->feed->id,
                'call_status_option_id' => $option['id'],
                'comments' => $this->comment,
                'campaign_id' => $this->campaign,
                'updated_by' => Auth::id(),
                'call_status_option_type' =>$option['type'],
            ]);
        }

        $this->emit('FeedCompleted');
        $this->CallStatusModal = false;

    }


    public function render()
    {
        return view('livewire.leads.partials.submit-call-status');
    }
}
