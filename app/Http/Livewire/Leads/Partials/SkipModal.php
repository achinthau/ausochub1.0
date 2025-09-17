<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\FeedContactAttempt;
use App\Models\FeedContactValid;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SkipModal extends Component
{
    public $SkipContactModal = false;
    public $feedId;
    public $comment='';

    protected $listeners = ['openSkipContactModal' => 'openModal'];

    public function openModal($phone, $feed_id)
    {
        $this->SkipContactModal = true;
        $this->phone = $phone;
        $this->feedId = $feed_id;
    }

    protected $rules = [
        'comment' => 'required|string|min:5',
    ];
    public function skipContact()
{
    $this->validate();

    $feeds = FeedContactValid::where('feed_id', $this->feedId)
        ->where(function ($q) {
            $q->where('contact_no_01', $this->phone)
              ->orWhere('contact_no_02', $this->phone);
        })
        ->get();

    foreach ($feeds as $feed) {
        $feed->status = 3; // skipped
        $feed->save();
    }

        FeedContactAttempt::create([
            'feed_id'      => $feed->feed_id,
            'contact_no_01'=> $feed->contact_no_01,
            'contact_no_02'=> $feed->contact_no_02,
            'status'       => '3',
            'comments'     => $this->comment,
            'updated_by'   => Auth::id(),
        ]);
    

    $this->SkipContactModal = false;
    $this->reset('comment');
    $this->emitTo('dashboard.partials.dialer.call-panel', 'contactSkipped');

}


    //skip status = 3
    public function render()
    {
        return view('livewire.leads.partials.skip-modal');
    }
}
