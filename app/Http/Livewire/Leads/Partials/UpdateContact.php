<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\CxTicket;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
use App\Models\FeedContactValid;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateContact extends Component
{
    public $UpdateContactModal = false;
    public $feedId;
    public $serviceType;
    public $comment = '';

    protected $listeners = ['openUpdateContactModal' => 'openModal'];

    public function openModal($phone,$phone2, $feed_id, $service)
    {
        $this->serviceType = $service;
        $this->UpdateContactModal = true;
        $this->phone = $phone;
        $this->phone2 = $phone2;
        $this->feedId = $feed_id;
    }

    protected $rules = [
        'comment' => 'required|string|min:9',
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
                    ->orWhere('customer_contact_01', $this->phone2)
                    ->orWhere('customer_contact_02', $this->phone2);
            })
                ->where('status', 'Closed')
                ->get();

            foreach ($surveyTickets as $surveyTicket) {
                // $surveyTicket->status = 'Skip';
                $surveyTicket->change_request = $this->comment;
                $surveyTicket->surveyed_by = Auth::user()->name;
                $surveyTicket->save();
            }
        } 
        
            // $feeds = FeedContactValid::where('feed_id', $this->feedId)
            //     ->where(function ($q) {
            //         $q->where('contact_no_01', $this->phone)
            //             ->orWhere('contact_no_02', $this->phone);
            //     })
            //     ->get();

            // foreach ($feeds as $feed) {
            //     $feed->status = 3; // skipped
            //     $feed->save();

            //     $option = DialerCallStatusOption::where('option', 'Skip')->first();
            //     // dd($option);

            //     FeedContactAttempt::create([
            //         'feed_contact_valid_id' => $feed->id,
            //         'call_status_option_id' => $option ? $option->id : '0',
            //         'comments' => $this->comment,
            //         'updated_by' => Auth::id(),
            //     ]);
            // }
        


        $this->UpdateContactModal = false;
        $this->reset('comment');

    }

    public function render()
    {
        return view('livewire.leads.partials.update-contact');
    }
}
