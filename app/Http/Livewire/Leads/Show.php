<?php

namespace App\Http\Livewire\Leads;

use App\Models\CallbackCustomer;
use App\Models\CallCount;
use App\Models\Campaign;
use App\Models\CxTicket;
use App\Models\FeedContactValid;
use App\Models\Lead;
use App\Models\QueueCount;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;
use WireUi\Traits\Actions;

class Show extends Component
{
    use Actions;

    public Lead $lead;
    public $tickets;
    public $callLogs;
    public $outCallLogs;
    public $timelineLogs;

    public $isIncomming = false;

    public $moodStatus = false;
    public $comment = '';
    public $isNuisance = false;

    public $callBack = false;
    public $callbackDate;
    public $callbackTime;
    public $callbackComment;

    public $feedContacts = [];
    public $selectedFeedContact = null;
    public $selectedContact;
    public $phone1;
    public $phone2;
    public $feed_id;
    public $boundType = '';
    public $campaign;
    public $service_type;
    public $surveyContacts;

    protected $listeners = ['refreshCard' => 'refreshCard', 'FeedCompleted' => '$refresh'];

    protected $rules = [
        'lead.contact_number' => 'required',
        'lead.skill.skillname' => 'required',
        'lead.first_name' => 'required',
        'lead.last_name' => 'nullable',
        'lead.nic' => 'nullable',
        'lead.address_line_1' => 'nullable',
        'lead.address_line_2' => 'nullable',
        'lead.city' => 'nullable',
        'lead.contact_number_2' => 'nullable',
        'lead.email' => 'nullable|email',
        //'lead.priority_level_id'=>'required',
        //'lead.satisfaction_level_id'=>'required',

    ];



    public function toggle()
    {
        $this->moodStatus = !$this->moodStatus;
        // if($this->isIncomming)
        // {
        //     $ani = $this->lead->contact_number;

        // if ($ani) {
        //     $latestRecord = QueueCount::where('ani', $ani)
        //         ->where('status', 2)
        //         ->orderBy('id', 'desc')
        //         ->first();

        //     if ($latestRecord) {
        //         if ($this->moodStatus) {
        //             // Toggle is ON (unsatisfied) – store reaction only
        //             $latestRecord->customer_reaction = 1;
        //             $latestRecord->save();
        //         } else {
        //             // Toggle is OFF – clear reaction and comment
        //             $latestRecord->customer_reaction = null;
        //             $latestRecord->comment = null;
        //             $latestRecord->save();

        //             // Also clear local state if needed
        //             $this->comment = '';
        //             session()->forget('message');
        //         }
        //     }
        // }
        // }
        // else{
        //     $dnis = $this->lead->contact_number;

        // if ($dnis) {
        //     $latestRecord = CallCount::where('dnis', $dnis)
        //         ->where('status', 1)
        //         ->orderBy('id', 'desc')
        //         ->first();

        //     if ($latestRecord) {
        //         if ($this->moodStatus) {
        //             // Toggle is ON (unsatisfied) – store reaction only
        //             $latestRecord->customer_reaction = 1;
        //             $latestRecord->save();
        //         } else {
        //             // Toggle is OFF – clear reaction and comment
        //             $latestRecord->customer_reaction = null;
        //             $latestRecord->comment = null;
        //             $latestRecord->save();

        //             // Also clear local state if needed
        //             $this->comment = '';
        //             session()->forget('message');
        //         }
        //     }
        // }
        // }
    }

    public function nuisance()
    {
        $this->isNuisance = !$this->isNuisance;
    }


    public function submitReaction()
    {
        if ($this->isNuisance) {
            $reaction = 2;
        } elseif ($this->moodStatus) {
            $reaction = 1;
        }

        if ($this->isIncomming) {
            $ani = $this->lead->contact_number;

            if ($ani) {
                $latestRecord = QueueCount::where('ani', $ani)->where('status', 2)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($latestRecord) {
                    $latestRecord->customer_reaction = $reaction;
                    $latestRecord->comment = $this->comment;
                    $latestRecord->save();
                }
            }
        } else {
            $dnis = $this->lead->contact_number;

            if ($dnis) {
                $latestRecord = CallCount::where('dnis', $dnis)->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($latestRecord) {
                    $latestRecord->customer_reaction = $reaction;
                    $latestRecord->comment = $this->comment;
                    $latestRecord->save();
                }
            }
        }

        session()->flash('message', 'Feedback submitted successfully!');
        // $this->moodStatus = false;
        $this->comment = '';
    }


    public function mount($lead)
    {
        $this->lead = $lead->load('tickets', 'tickets.category', 'tickets.status', 'tickets.outlet', 'orders', 'orders.items');
        $this->isIncomming = filter_var(request()->query('isIncomming'), FILTER_VALIDATE_BOOLEAN);
        $this->feed_id = request()->query('feed');
        $this->campaign = request()->query('cmp');
        $this->service_type = Campaign::where('name', $this->campaign)->value('service_type');
        // $this->campaign = Campaign::whereIn('assigned_feeds', [$this->feed_id])->get();
        // dd($this->campaign);

        $userId = Auth::user()->id;
        $boundType = Redis::get("user:{$userId}:bound_type");
        $this->boundType = $boundType;
        if ($boundType && $boundType == 'dialer' && $this->service_type != 'survey') {
            $phone = $this->lead->contact_number;
            $this->selectedContact = $phone;
            // $this->feedContacts = FeedContactValid::where('contact_no_01', $phone)->orWhere('contact_no_02', $phone)->get();
            $feedId = $this->feed_id;
            $this->feedContacts = FeedContactValid::where(function ($query) use ($phone) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone);
            })
                ->when($feedId, function ($query, $feedId) {
                    $query->where('feed_id', $feedId); // filter by feed_id if present
                })
                ->get();


            if ($this->feedContacts->isNotEmpty()) {
                $foundContact = $this->feedContacts->first();
                if ($foundContact->contact_no_01 === $phone) {
                    $this->phone2 = $foundContact->contact_no_02;
                } else {
                    $this->phone2 = $foundContact->contact_no_01;
                }
            } else {
                $this->phone2 = null;
            }

            // dd($phone);
        }




        if ($boundType && $boundType == 'dialer' && $this->service_type == 'survey') {
            $phone = $this->lead->contact_number;
            $this->selectedContact = $phone;

            $this->surveyContacts = CxTicket::where(function ($query) use ($phone) {
                $query->where('customer_contact_01', $phone)
                    ->orWhere('customer_contact_02', $phone);
            })
                ->get();

            if ($this->surveyContacts->isNotEmpty()) {
                $foundContact = $this->surveyContacts->first();
                if ($foundContact->customer_contact_01 === $phone) {
                    $this->phone2 = $foundContact->customer_contact_02;
                } else {
                    $this->phone2 = $foundContact->customer_contact_01;
                }
            } else {
                $this->phone2 = null;
            }
        }
    }

    public function render()
    {
        return view('livewire.leads.show');
    }

    public function refreshCard()
    {
        $this->lead->refresh();
        $this->refreshTimeline();
    }

    public function refreshTimeline()
    {
        $this->emitTo('leads.partials.activity-log', 'refreshTimeline');
    }

    public function save()
    {
        $this->validate();
        if (!empty($this->lead->contact_number) && strlen($this->lead->contact_number) === 9) {
            // $this->lead->contact_number = substr($this->lead->contact_number, 1);
            $this->lead->contact_number = '0' . $this->lead->contact_number;
        }
        $this->lead->status_id = 2;
        $this->lead->save();

        $this->notification()->success(
            $title = 'Success',
            $description = 'Customer successfull saved'
        );
    }

    public function openWhatsApp()
    {

        $number = preg_replace('/\s+/', '', $this->lead->whatsapp);

        if (str_starts_with($number, '94')) {
            $internationalNumber = $number;
        } elseif (str_starts_with($number, '0')) {
            $internationalNumber = '94' . substr($number, 1);
        } else {
            $internationalNumber = '94' . $number;
        }
        // dd($internationalNumber);

        $message = urlencode('Hello! I would like to chat with you.');
        $url = "https://wa.me/{$internationalNumber}?text={$message}";


        $this->emit('whatsappOpened', $url);
    }


    public function toggleCallbackCustomer()
    {
        $this->callBack = !$this->callBack;
    }

    public function saveCallback()
    {
        $this->validate([
            'callbackDate' => 'required|date',
            'callbackTime' => 'required',
            'callbackComment' => 'nullable|string',
        ]);

        CallbackCustomer::create([
            'agent_id' => auth()->id(),
            'lead_id' => $this->lead->id,
            'unique_id' => $this->lead->unique_id,
            'contact_number' => $this->lead->contact_number,
            'src' => 'lead',
            'callback_at' => Carbon::parse("{$this->callbackDate} {$this->callbackTime}"),
            'comment' => $this->callbackComment,
        ]);

        session()->flash('messagedialog', 'Callback saved successfully.');

        // Optionally reset
        $this->reset(['callBack', 'callbackDate', 'callbackTime', 'callbackComment']);
    }

    public function makeCall($phone)
    {
        $user = Auth::user()->load([
            'agent',
            'agent.extensionDetails',
            'currentQueues'
        ]);

        $currentSkills = $user->currentQueues()->active()->pluck('skill')->unique();

        // Get the only skill if there's exactly one
        $campaignName = $currentSkills->first();
        $campHotline = $campaignName
            ? Campaign::where('name', $campaignName)->value('hotline')
            : null;
//         dd([
//     'currentSkills' => $currentSkills,
//     'campaignName' => $campaignName,
//     'campHotline' => $campHotline,
// ]);


        $extension = Auth::user()->extension;
        $tenant_context = Auth::user()->tenant_context;
        $url = env('CALL_SERVER_API_URL') . '/dialscripts/dial.php';



        $response = $response = Http::get($url, [
            'type' => '2',
            'exten' => $extension,
            'num' => $phone,
            'tenant' => $tenant_context,
            'uid' => 22,
            'dialer' => $this->campaign,
            'cmphotline' => $campHotline,
        ]);

        if ($response->successful()) {
            $this->dispatchBrowserEvent('notify', ['message' => 'API call successful!']);
        } else {
            $this->dispatchBrowserEvent('notify', ['message' => 'API call failed!']);
        }
    }


}
