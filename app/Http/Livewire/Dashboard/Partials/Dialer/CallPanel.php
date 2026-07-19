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
    public $phone2;
    public $customerName;
    public $addressLine1;
    public $addressLine2;
    public $feed_id;
    public $campaignName;
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
        $currentSkills = Auth::user()->currentQueues()->active()->pluck('skill')->unique();

        if ($currentSkills->isNotEmpty()) {
            $this->displayNumber = true;
        } else {
            $this->displayNumber = false;
        }
        // dd($currentSkills);
        $userId = Auth::id();

        // 1. Find active campaigns where user is assigned
        // $campaigns = Campaign::where('status', 1)
        //     ->get()
        //     ->filter(function ($campaign) use ($userId) {
        //         $assignedUsers = $campaign->assigned_users
        //             ? array_filter(explode(',', $campaign->assigned_users))
        //             : [];
        //         return in_array($userId, $assignedUsers);
        //     });
        $campaigns = Campaign::where('status', 1)
            ->whereIn('name', $currentSkills)
            ->get();

        // dd($campaigns);
        //status and name index 




        // 2. Collect feed IDs from these campaigns
        $feedIds = $campaigns->flatMap->feed_ids->unique()->toArray();

        // 3. Find first available contact for those feeds
        $record = FeedContactValid::whereIn('feed_id', $feedIds)
            ->where(function ($query) {
                $query->whereNull('status') // Fresh ones
                    ->orWhereIn('status', [2, 22]); // No Answer retries
            })
            ->where(function ($query) {
                $query->whereNull('next_available_at')
                    ->orWhere('next_available_at', '<=', now());
            })
            ->where(function ($query) use ($userId) {
                $query->whereNull('assigned_to')        // unassigned
                    ->orWhere('assigned_to', $userId); // or already assigned to this user
            })
            ->first();

            // indexes for feed_id, status

        if ($record) {
            $userId = Auth::id();
            $phone = $record->contact_no_01 ?? $record->contact_no_02;
            $phone2 = $record->contact_no_02 ?? $record->contact_no_01;
            $feedId = $record->feed_id;

            // 1. Find all rows with this number across all feeds
            $relatedContacts = FeedContactValid::where(function ($query) use ($phone,$phone2) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone)
                    ->orWhere('contact_no_01', $phone2)
                    ->orWhere('contact_no_02', $phone2);
            })
                ->whereNull('status');
            // ->where('feed_id',$feedId); 

            // 2. Assign all of them to the current agent
            $relatedContacts->update(['assigned_to' => $userId]);
            $this->contact = $record;
            $this->phone = !empty($record->contact_no_01) ? $record->contact_no_01 : $record->contact_no_02;
            $this->phone2 = !empty($record->contact_no_02) ? $record->contact_no_02 : $record->contact_no_01;
            $this->feed_id = $record->feed_id;

            $data = json_decode($record->data, true);
            $this->customerName = $data['cust_name'] ?? null;
            $this->addressLine1 = $data['add1'] ?? null;
            $this->addressLine2 = $data['add2'] ?? null;
            // $this->addressLine2 = $data;

            $campaignForNumber = $campaigns->first(function ($campaign) use ($feedId) {
                // Assuming $campaign->feed_ids is array
                $feedIds = is_array($campaign->feed_ids) ? $campaign->feed_ids : json_decode($campaign->feed_ids, true);
                return in_array($feedId, $feedIds);
            });

            $this->campaignName = $campaignForNumber ? $campaignForNumber->name : null;

        } else {
            $this->phone = null;
            $this->reason = 'No available contacts in your assigned campaigns.';
        }
    }

    public function openProfile($phone, $phone2)
    {
        // dd($this->feed_id);
        // dd($this->campaignName);
        $number = !empty($phone) ? $phone : $phone2;
        $number2 = !empty($phone) ? $phone2 : $phone;

        // dd($this->addressLine2);

        // If it's only 9 digits, add the 0
        // if (!empty($number) && strlen($number) === 9) {
        //     $number = '0' . $number;
        // }

        // Try to find lead
        // $lead = Lead::where('contact_number', $number)->first();
        // $lead = Lead::where('contact_number', 'LIKE', "%{$number}%")->first();
        $lead = Lead::where(function ($query) use ($number, $number2) {
        if (!empty($number)) {
            $query->where('contact_number', 'LIKE', "%{$number}%")
                  ->orWhere('contact_number_2', 'LIKE', "%{$number}%");
        }

        if (!empty($number2)) {
            $query->orWhere('contact_number', 'LIKE', "%{$number2}%")
                  ->orWhere('contact_number_2', 'LIKE', "%{$number2}%");
        }
    })->first();


        if (!$lead) {
            // If not found, create new one
            $lead = new Lead();
            $lead->contact_number = $number;
            $lead->first_name = $this->customerName;
            $lead->address_line_1 = $this->addressLine1;
            $lead->address_line_2 = $this->addressLine2;
            $lead->status_id = 1; // new lead status (same as in your old code)
            $lead->agent_id = auth()->user()->id ?? null; // if user has agent
            $lead->extension = auth()->user()->extension ?? null;
            $lead->skill_id = 0; // or detect skill like in your old code
            $lead->save();
        }

        $url = route('leads.show', ['lead' => $lead->id]) . '?feed=' . $this->feed_id . '&cmp=' . $this->campaignName;

        return redirect()->to($url);
    }


}
