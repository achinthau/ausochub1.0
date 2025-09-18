<?php

namespace App\Http\Livewire\Dashboard\Partials\Dialer;

use App\Models\Campaign;
use App\Models\FeedContactValid;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CallPanel extends Component
{
    public $phone;
    public $feed_id;
    public $reason = null;

    public $displayNumber = false;

    protected $listeners = ['contactSkipped' => 'loadContact', 'updateSkill' => 'displayPhone'];

    public function mount()
    {
        $currentSkills = Auth::user()->currentQueues()->active()->get();

        if ($currentSkills->isNotEmpty()) {
            $this->displayNumber = true;
        } else {
            $this->displayNumber = false;
        }
        // dd($currentSkills);
        $this->loadContact();
    }
    public function displayPhone()
    {
        $this->loadContact();
    }

    public function loadContact()
    {
        $currentSkills = Auth::user()->currentQueues()->active()->get();

        if ($currentSkills->isNotEmpty()) {
            $this->displayNumber = true;
        } else {
            $this->displayNumber = false;
        }
        // dd($currentSkills);
        $userId = Auth::id();

        // 1. Find active campaigns where user is assigned
        $campaigns = Campaign::where('status', 1)
            ->get()
            ->filter(function ($campaign) use ($userId) {
                $assignedUsers = $campaign->assigned_users
                    ? array_filter(explode(',', $campaign->assigned_users))
                    : [];
                return in_array($userId, $assignedUsers);
            });

        // 2. Collect feed IDs from these campaigns
        $feedIds = $campaigns->flatMap->feed_ids->unique()->toArray();

        // 3. Find first available contact for those feeds
        $record = FeedContactValid::whereIn('feed_id', $feedIds)
            ->whereNull('status')
            ->where(function ($query) use ($userId) {
                $query->whereNull('assigned_to')        // unassigned
                    ->orWhere('assigned_to', $userId); // or already assigned to this user
            })
            ->first();

        if ($record) {
            $userId = Auth::id();
            $phone = $record->contact_no_01 ?? $record->contact_no_02;

            // 1. Find all rows with this number across all feeds
            $relatedContacts = FeedContactValid::where(function ($query) use ($phone) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone);
            })
                ->whereNull('status'); // Only unprocessed numbers

            // 2. Assign all of them to the current agent
            $relatedContacts->update(['assigned_to' => $userId]);
            $this->contact = $record;
            $this->phone = $record->contact_no_01 ?? $record->contact_no_02;
            $this->feed_id = $record->feed_id;
        } else {
            $this->phone = null;
            $this->reason = 'No available contacts in your assigned campaigns.';
        }
    }

    public function openProfile($phone)
    {
        // dd($this->feed_id);
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
        $url = route('leads.show', ['lead' => $lead->id]) . '?feed=' . $this->feed_id;

        $this->dispatchBrowserEvent('open-lead-window', [
            'url' => $url,
            'lead_id' => $lead->id,
            'feed_id' => $this->feed_id,
        ]);
    }



    public function render()
    {
        return view('livewire.dashboard.partials.dialer.call-panel');
    }


}
