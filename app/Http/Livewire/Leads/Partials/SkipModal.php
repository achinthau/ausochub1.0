<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\CxTicket;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
use App\Models\FeedContactValid;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SkipModal extends Component
{
    public $SkipContactModal = false;
    public $feedId;
    public $serviceType;
    public $comment = '';

    protected $listeners = ['openSkipContactModal' => 'openModal'];

    public function openModal($phone,$phone2, $feed_id, $service)
    {
        $this->serviceType = $service;
        $this->SkipContactModal = true;
        $this->phone = $phone;
        $this->phone2 = $phone2;
        $this->feedId = $feed_id;
    }

    protected $rules = [
        'comment' => 'required|string|min:5',
    ];
    public function skipContact()
    {
        $this->validate();
        $phone = $this->phone;
        $phone2 = $this->phone2;

        if ($this->serviceType == 'satisfaction') {
            $surveyTickets = CxTicket::where(function ($query) use ($phone,$phone2) {
                $query->where('customer_contact_01', $phone)
                    ->orWhere('customer_contact_02', $phone)
                    ->orWhere('customer_contact_01', $phone2)
                    ->orWhere('customer_contact_02', $phone2);
            })
                ->where('status', 'Closed')
                ->get();

            foreach ($surveyTickets as $surveyTicket) {
                $surveyTicket->status = 'Skip';
                $surveyTicket->skipped_reasons = $this->comment;
                $surveyTicket->skipped_by = Auth::user()->name;
                $surveyTicket->save();
            }
        } 
        
            $feeds = FeedContactValid::where('feed_id', $this->feedId)
                ->where(function ($q) {
                    $q->where('contact_no_01', $this->phone)
                        ->orWhere('contact_no_02', $this->phone)
                    ->orWhere('contact_no_01', $this->phone2)
                    ->orWhere('contact_no_02', $this->phone2);
                })
                ->get();

            foreach ($feeds as $feed) {
                $feed->status = 3; // skipped
                $feed->save();

                $option = DialerCallStatusOption::where('option', 'Skip')->first();
                // dd($option);

                FeedContactAttempt::create([
                    'feed_contact_valid_id' => $feed->id,
                    'call_status_option_id' => $option ? $option->id : '0',
                    'comments' => $this->comment,
                    'updated_by' => Auth::id(),
                ]);
            }
        


        $this->SkipContactModal = false;
        $this->reset('comment');
        $this->emitTo('dashboard.partials.dialer.call-panel', 'contactSkipped');
        $this->dispatchBrowserEvent('close-skipped-tab');

    }


    //skip status = 3
    public function render()
    {
        return view('livewire.leads.partials.skip-modal');
    }
}
