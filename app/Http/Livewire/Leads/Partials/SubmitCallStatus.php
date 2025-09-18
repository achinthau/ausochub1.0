<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
use App\Models\FeedContactValid;
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

    public $applyToAll =false;

    protected $listeners = ['openCallStatusModal' => 'openModal'];

    public function openModal($id, $status, $apply)
    {
        $this->CallStatusModal = true;
        $this->applyToAll = $apply;
        $this->feed = FeedContactValid::find($id);
        // dd($this->feed);
        $this->status = $status;
        $this->options = DialerCallStatusOption::where('type', $status === 'answered' ? 1 : 2)
            ->pluck('option', 'id')
            ->toArray();

        $this->selectedOption = null;
        $this->comment = '';
    }

    // protected $rules = [
    //     // 'comment' => 'required|string|min:5',
    //     'selectedOption' => 'required|exists:dialer_call_status_options,id',
    // ];

    protected $messages = [
    'comment.required' => 'Please select a payment date.',
    'comment.date'     => 'The payment date must be a valid date.',
    'comment.after_or_equal' => 'The payment date cannot be in the past.',
    'selectedOption.required' => 'You must select a call status.',
];


    public function submit()
{
    $option = DialerCallStatusOption::find($this->selectedOption);

    // Validation
    if ($option && $option->option === 'Promised to pay') {
        $this->validate([
            'comment' => 'required|date|after_or_equal:today',
            'selectedOption' => 'required|exists:dialer_call_status_options,id',
        ]);
    } else {
        $this->validate([
            'selectedOption' => 'required|exists:dialer_call_status_options,id',
        ]);
    }

    if ($this->applyToAll) {
        // Apply to all feed contacts with the same number(s)
        $phone = $this->feed->contact_no_01 ?? $this->feed->contact_no_02;

        $feeds = FeedContactValid::where(function ($query) use ($phone) {
                $query->where('contact_no_01', $phone)
                      ->orWhere('contact_no_02', $phone);
            })
            ->when($this->feed->feed_id, function ($query, $feedId) {
                // optional filter by feed_id
                $query->where('feed_id', $feedId);
            })
            ->get();

        foreach ($feeds as $feed) {
            $feed->status = $this->status === 'answered' ? 1 : 2;
            $feed->save();

            FeedContactAttempt::create([
                'feed_contact_valid_id' => $feed->id,
                'call_status_option_id' => $this->selectedOption,
                'comments'             => $this->comment,
                'updated_by'           => Auth::id(),
            ]);
        }
    } else {
        // Only update the current feed
        $this->feed->status = $this->status === 'answered' ? 1 : 2;
        $this->feed->save();

        FeedContactAttempt::create([
            'feed_contact_valid_id' => $this->feed->id,
            'call_status_option_id' => $this->selectedOption,
            'comments'             => $this->comment,
            'updated_by'           => Auth::id(),
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
