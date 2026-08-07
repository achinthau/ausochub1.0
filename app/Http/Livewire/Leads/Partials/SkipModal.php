<?php

namespace App\Http\Livewire\Leads\Partials;

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
                $feed->call_status_option_id = null;
                $feed->call_status_option_type = 'skip';
                $feed->comments = $this->comment;
                $feed->updated_by = Auth::id();
                $feed->attempted_at = now();
                $feed->save();
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
