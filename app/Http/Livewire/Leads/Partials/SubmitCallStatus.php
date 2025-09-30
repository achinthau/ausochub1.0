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
    public $feedCount =0;
    public $paymentDate;

    protected $listeners = ['openCallStatusModal' => 'openModal'];

    public function openModal($id, $status, $feedCount)
    {
        $this->CallStatusModal = true;
        $this->feedCount = $feedCount;
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
    // 'comment.required' => 'Please select a payment date.',
    'paymentDate.required'     => 'The payment date must be a valid date.',
    'paymentDate.after_or_equal' => 'The payment date cannot be in the past.',
    'selectedOption.required' => 'You must select a call status.',
];


    public function submit()
{
    $option = DialerCallStatusOption::find($this->selectedOption);

    // Validation
    if ($option && $option->option === 'Promised to pay') {
        $this->validate([
            'paymentDate' => 'required|date|after_or_equal:today',
            'selectedOption' => 'required|exists:dialer_call_status_options,id',
        ]);
        $this->comment = $this->comment . 'Payment Date: '. $this->paymentDate;
    } else {
        $this->validate([
            'selectedOption' => 'required|exists:dialer_call_status_options,id',
        ]);
    }

    if ($this->applyToAll) {
    // Get the contact number
    $phone = $this->feed->contact_no_01 ?? $this->feed->contact_no_02;

    // Fetch only feeds with same number AND not updated before
    $feeds = FeedContactValid::where(function ($query) use ($phone) {
            $query->where('contact_no_01', $phone)
                  ->orWhere('contact_no_02', $phone);
        })
        ->whereNull('status') // ✅ Only update UNTOUCHED records
        ->when($this->feed->feed_id, function ($query, $feedId) {
            $query->where('feed_id', $feedId);
        })
        ->get();

    foreach ($feeds as $feed) {
        $feed->status = $this->status === 'answered' ? 1 : 2;
        $feed->save();

        FeedContactAttempt::create([
            'feed_contact_valid_id' => $feed->id,
            'call_status_option_id' => $this->selectedOption,
            'comments'              => $this->comment,
            'updated_by'            => Auth::id(),
        ]);
    }
} else {
    // ✅ Update only the current feed
    $this->feed->status = $this->status === 'answered' ? 1 : 2;
    $this->feed->save();

    FeedContactAttempt::create([
        'feed_contact_valid_id' => $this->feed->id,
        'call_status_option_id' => $this->selectedOption,
        'comments'              => $this->comment,
        'updated_by'            => Auth::id(),
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
