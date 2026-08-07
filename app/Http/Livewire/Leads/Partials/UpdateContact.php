<?php

namespace App\Http\Livewire\Leads\Partials;

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
                $surveyFeed->call_status_option_id = null;
                $surveyFeed->call_status_option_type = 'change_request';
                $surveyFeed->comments = $this->comment;
                $surveyFeed->updated_by = Auth::id();
                $surveyFeed->attempted_at = now();
                $surveyFeed->save();
            }
        } 
        $this->UpdateContactModal = false;
        $this->reset('comment');

    }

    public function render()
    {
        return view('livewire.leads.partials.update-contact');
    }
}
