<?php

namespace App\Http\Livewire\Dashboard\Partials\Dialer;

use App\Models\FeedContactValid;
use App\Models\Lead;
use Livewire\Component;

class CallPanel extends Component
{
    public $phone;
    public function mount()
    {
        $record = FeedContactValid::whereNull('status')->first();

        $this->phone = $record?->phone;
    }

    public function openProfile($phone)
{
    // dd('hg');
    $number = $phone;

    // If it's only 9 digits, add the 0
    // if (!empty($number) && strlen($number) === 9) {
    //     $number = '0' . $number;
    // }

    // Try to find lead
    $lead = Lead::where('contact_number', $number)->first();

    if (!$lead) {
        // If not found, create new one
        $lead = new Lead();
        $lead->contact_number = $number;
        $lead->status_id = 1; // new lead status (same as in your old code)
        $lead->agent_id = auth()->user()->id ?? null; // if user has agent
        $lead->extension = auth()->user()->extension ?? null;
        $lead->skill_id = 0; // or detect skill like in your old code
        $lead->save();
    }

    // Emit browser event to open new tab/window
    $url = route('leads.show', $lead->id);
    $this->dispatchBrowserEvent('open-lead-window', [
        'url' => $url,
        'lead_id' => $lead->id,
    ]);
}



    public function render()
    {
        return view('livewire.dashboard.partials.dialer.call-panel');
    }


}
