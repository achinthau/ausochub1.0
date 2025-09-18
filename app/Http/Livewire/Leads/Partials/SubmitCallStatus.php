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

    protected $listeners = ['openCallStatusModal' => 'openModal'];

    public function openModal($id, $status)
    {
        $this->CallStatusModal = true;
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
        // $this->validate();
        $option = DialerCallStatusOption::find($this->selectedOption);

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

        $this->feed->status = $this->status === 'answered' ? 1 : 2;
        $this->feed->save();

        FeedContactAttempt::create([
            'feed_contact_valid_id' => $this->feed->id,
            'call_status_option_id' => $this->selectedOption,
            'comments' => $this->comment,
            'updated_by' => Auth::id(),
        ]);

        $this->emit('FeedCompleted');
        $this->CallStatusModal = false;

    }

    public function render()
    {
        return view('livewire.leads.partials.submit-call-status');
    }
}
