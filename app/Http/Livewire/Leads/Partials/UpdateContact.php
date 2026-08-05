<?php

namespace App\Http\Livewire\Leads\Partials;

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
            $surveyFeeds = FeedContactValid::where(function ($query) use ($phone,$phone2) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone)
                    ->orWhere('contact_no_01', $this->phone2)
                    ->orWhere('contact_no_02', $this->phone2);
            })
                ->get();

            foreach ($surveyFeeds as $surveyFeed) {
                FeedContactAttempt::create([
                    'feed_contact_valid_id' => $surveyFeed->id,
                    'call_status_option_id' => null,
                    'call_status_option_type' => 'change_request',
                    'comments' => $this->comment,
                    'updated_by' => Auth::id(),
                ]);
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
