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

    public function openModal($id,$status)
    {
        $this->CallStatusModal = true;
        $this->feed = FeedContactValid::find($id);
        // dd($this->feed);
        $this->status= $status;
        $this->options = DialerCallStatusOption::where('type', $status === 'answered' ? 1 : 2)
            ->pluck('option', 'id') 
            ->toArray();

        $this->selectedOption = null;
        $this->comment = '';
    }

    protected $rules = [
        'comment' => 'required|string|min:5',
        'selectedOption' => 'required|exists:dialer_call_status_options,option',
    ];

    public function submit()
    {
        $this->validate();
        $this->feed->status = $this->status === 'answered' ? 1 : 2;
        $this->feed->save();

        FeedContactAttempt::create([
            'feed_contact_valid_id' => $this->feed->id,
            'comments'     => $this->selectedOption .'->'. $this->comment,
            'updated_by'   => Auth::id(),
        ]);

        $this->emit('FeedCompleted');
        $this->CallStatusModal = false;

    }
    
    public function render()
    {
        return view('livewire.leads.partials.submit-call-status');
    }
}
