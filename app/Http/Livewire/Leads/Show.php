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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;

class Show extends Component
{
    use Actions, WithFileUploads;

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
    public $ticketInfoOpen = false;

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
    public $feedContactId;
    public $feedContactIdStatus;
    public $phone_numbers = [];
    public $whatsappModal = false;
    public $whatsappMessage = '';
    public $notifyWhatsApp = false;
    public $notifyEmail = false;
    public $notifyPhone = false;
    public $channelError = '';
    public $attachment;
    public $attachmentResetKey = 0;

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
            $dnisCandidates = $this->dialerReactionCandidates();

            if (!empty($dnisCandidates)) {
                $latestRecord = CallCount::whereIn('dnis', $dnisCandidates)
                    ->where('direction', 'out')
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($latestRecord) {
                    $latestRecord->customer_reaction = $reaction;
                    $latestRecord->comment = $this->comment;
                    $latestRecord->save();

                    // Keep report table in sync for dialer reports.
                    DB::connection('mysql-old')
                        ->table('au_callcount_report')
                        ->where('uniqueid', $latestRecord->uniqueid)
                        ->whereIn('direction', ['out', 'disout'])
                        ->update([
                            'customer_reaction' => $reaction,
                            'comment' => $this->comment,
                        ]);
                } else {
                    // Fallback when uniqueid linkage is missing: update the latest outbound report row by dialed number.
                    $reportRow = DB::connection('mysql-old')
                        ->table('au_callcount_report')
                        ->select('id')
                        ->whereIn('dnis', $dnisCandidates)
                        ->where('direction', 'out')
                        ->where('status', 1)
                        ->orderByDesc('id')
                        ->first();

                    if ($reportRow) {
                        DB::connection('mysql-old')
                            ->table('au_callcount_report')
                            ->where('id', $reportRow->id)
                            ->update([
                                'customer_reaction' => $reaction,
                                'comment' => $this->comment,
                            ]);
                    }
                }
            }
        }

        session()->flash('message', 'Feedback submitted successfully!');
        // $this->moodStatus = false;
        $this->comment = '';
    }

    protected function normalizePhoneNumber($phone): string
    {
        $phone = preg_replace('/\s+/', '', (string) $phone);

        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return substr($phone, 1);
        }

        return $phone;
    }

    protected function dialerPhoneCandidates(?string $phone): array
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return [];
        }

        $candidates = [$digits];

        if (strlen($digits) === 9) {
            $local = '0' . $digits;
            $candidates[] = $local;
            $candidates[] = '94' . $digits;
            $candidates[] = '9' . $local;
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $local = substr($digits, 1);
            $candidates[] = $local;
            $candidates[] = '94' . $local;
            $candidates[] = '9' . $digits;
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '94')) {
            $local = substr($digits, 2);
            $candidates[] = $local;
            $candidates[] = '0' . $local;
            $candidates[] = '9' . '0' . $local;
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    protected function dialerReactionCandidates(): array
    {
        $phones = array_filter([
            $this->selectedContact,
            $this->lead->contact_number ?? null,
            $this->lead->contact_number_2 ?? null,
        ]);

        $candidates = [];

        foreach ($phones as $phone) {
            $candidates = array_merge($candidates, $this->dialerPhoneCandidates($phone));
        }

        return array_values(array_unique($candidates));
    }

    protected function resolveLeadForContact(FeedContactValid $contact): Lead
    {
        $phone = $this->normalizePhoneNumber($contact->contact_no_01);
        $phone2 = $this->normalizePhoneNumber($contact->contact_no_02);

        $lead = Lead::where(function ($query) use ($phone, $phone2) {
            if ($phone !== '') {
                $query->where('contact_number', 'LIKE', "%{$phone}%")
                    ->orWhere('contact_number_2', 'LIKE', "%{$phone}%");
            }

            if ($phone2 !== '') {
                $query->orWhere('contact_number', 'LIKE', "%{$phone2}%")
                    ->orWhere('contact_number_2', 'LIKE', "%{$phone2}%");
            }
        })->first();

        if (!$lead) {
            $data = json_decode($contact->data ?? '{}', true);

            $lead = new Lead();
            $lead->contact_number = $phone !== '' ? $phone : $phone2;
            $lead->contact_number_2 = $phone2 !== '' ? $phone2 : null;
            $lead->first_name = $data['cust_name'] ?? $data['customer_name'] ?? null;
            $lead->address_line_1 = $data['add1'] ?? $data['address_line_1'] ?? null;
            $lead->address_line_2 = $data['add2'] ?? $data['address_line_2'] ?? null;
            $lead->status_id = 1;
            $lead->agent_id = auth()->id();
            $lead->extension = auth()->user()->extension ?? null;
            $lead->skill_id = 0;
            $lead->save();
        }

        return $lead;
    }

    protected function getCurrentDialerPanelContact(): array
    {
        $userId = Auth::id();
        $currentSkills = Auth::user()->currentQueues()->active()->pluck('skill')->unique();

        $campaigns = Campaign::where('status', 1)
            ->whereIn('name', $currentSkills)
            ->get();

        $feedIds = $campaigns->flatMap->feed_ids->unique()->toArray();

        if (empty($feedIds)) {
            return [null, null];
        }

        $record = FeedContactValid::whereIn('feed_id', $feedIds)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', [2, 22]);
            })
            ->where(function ($query) {
                $query->whereNull('next_available_at')
                    ->orWhere('next_available_at', '<=', now());
            })
            ->where(function ($query) use ($userId) {
                $query->whereNull('assigned_to')
                    ->orWhere('assigned_to', $userId);
            })
            ->first();

        if (!$record) {
            return [null, null];
        }

        $campaignForNumber = $campaigns->first(function ($campaign) use ($record) {
            $campaignFeedIds = is_array($campaign->feed_ids)
                ? $campaign->feed_ids
                : json_decode($campaign->feed_ids, true);

            return in_array($record->feed_id, $campaignFeedIds ?: []);
        });

        return [$record, $campaignForNumber ? $campaignForNumber->name : null];
    }

    public function nextContact()
    {
        if ($this->boundType !== 'dialer') {
            return;
        }

        [$currentContact, $campaignName] = $this->getCurrentDialerPanelContact();

        if (!$currentContact) {
            return;
        }

        $this->selectedFeedContact = $currentContact;
        $this->feedContactId = $currentContact->id;
        $this->feedContactIdStatus = $currentContact->status;

        $lead = $this->resolveLeadForContact($currentContact);

        return redirect()->route('leads.show', [
            'lead' => $lead->id,
            'feed' => $currentContact->feed_id,
            'cmp' => $campaignName ?? $this->campaign,
        ]);
    }


    public function mount($lead)
    {
//         \Log::info('Show Lead component mounting', ['lead_id' => $lead->id]);
        $this->lead = $lead->load('tickets', 'tickets.category', 'tickets.status', 'tickets.outlet', 'orders', 'orders.items');

        if (filled(trim((string) $this->lead->full_name))) {
            // $this->emitTo('tickets.create', 'showCreatingTicket');
            $this->emit('showCreatingTicket');
        }

        $this->isIncomming = filter_var(request()->query('isIncomming'), FILTER_VALIDATE_BOOLEAN);
        $this->feed_id = request()->query('feed');
        $this->campaign = request()->query('cmp');
        $this->service_type = Campaign::where('name', $this->campaign)->value('service_type');
        // $this->campaign = Campaign::whereIn('assigned_feeds', [$this->feed_id])->get();
        // dd($this->campaign);

        $userId = Auth::user()->id;
        $boundType = Redis::get("user:{$userId}:bound_type");
        $this->boundType = $boundType;

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
            $this->selectedFeedContact = $foundContact;
            $this->feedContactId = $foundContact->id;
            $this->feedContactIdStatus = $foundContact->status;

            if ($foundContact->contact_no_01 === $phone) {
                $this->phone2 = $foundContact->contact_no_02;
            } else {
                $this->phone2 = $foundContact->contact_no_01;
            }
        } else {
            $this->phone2 = null;
        }

        if ($boundType && $boundType == 'dialer' && $this->service_type != 'satisfaction') {
            $phone = $this->lead->contact_number;
            $phone2 = $this->phone2;
            $this->selectedContact = $phone;
            // $this->feedContacts = FeedContactValid::where('contact_no_01', $phone)->orWhere('contact_no_02', $phone)->get();
            $feedId = $this->feed_id;
            $this->feedContacts = FeedContactValid::where(function ($query) use ($phone, $phone2) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone);

                if (!empty($phone2)) {
                    $query->orWhere('contact_no_01', $phone2)
                        ->orWhere('contact_no_02', $phone2);
                }
            })
                ->when($feedId, fn($query) => $query->where('feed_id', $feedId))
                ->get();


            if ($this->feedContacts->isNotEmpty()) {
                $foundContact = $this->feedContacts->first();
                $this->selectedFeedContact = $foundContact;
                $this->feedContactId = $foundContact->id;
                $this->feedContactIdStatus = $foundContact->status;

                if ($foundContact->contact_no_01 === $phone) {
                    $this->phone2 = $foundContact->contact_no_02;
                } else {
                    $this->phone2 = $foundContact->contact_no_01;
                }
            } else {
                $this->phone2 = null;
            }


            $this->phone_numbers = [$phone];


            if ($this->feedContacts->isNotEmpty()) {
                $allContacts = $this->feedContacts->flatMap(function ($contact) {
                    return [$contact->contact_no_01, $contact->contact_no_02];
                });


                $normalized = $allContacts->map(function ($p) {
                    $p = preg_replace('/\s+/', '', $p);
                    if (strlen($p) === 10 && str_starts_with($p, '0')) {
                        return substr($p, 1);
                    }
                    return $p;
                });


                $mainPhoneNormalized = preg_replace('/\s+/', '', $phone);
                if (strlen($mainPhoneNormalized) === 10 && str_starts_with($mainPhoneNormalized, '0')) {
                    $mainPhoneNormalized = substr($mainPhoneNormalized, 1);
                }


                $this->phone_numbers = $normalized->push($mainPhoneNormalized)
                    ->unique()
                    ->values()
                    ->all();
            } else {

                $this->phone_numbers = [$phone];
            }


            // dd($phone);
        }




        if ($boundType && $boundType == 'dialer' && $this->service_type == 'satisfaction') {
            $phone = $this->lead->contact_number;
            $this->selectedContact = $phone;

            $feedId = $this->feed_id;
            // $this->feedContactId = FeedContactValid::where(function ($query) use ($phone) {
            //     $query->where('contact_no_01', $phone)
            //         ->orWhere('contact_no_02', $phone);
            // })
            //     ->when($feedId, function ($query, $feedId) {
            //         $query->where('feed_id', $feedId); // filter by feed_id if present
            //     })
            //     ->value('id');
            // dd($this->feedContactId);

            $query = FeedContactValid::where(function ($query) use ($phone) {
                $query->where('contact_no_01', $phone)
                    ->orWhere('contact_no_02', $phone);

                if ($this->phone2) {
                    $query->orWhere('contact_no_01', $this->phone2)
                        ->orWhere('contact_no_02', $this->phone2);
                }
            })
                ->when($feedId, function ($query, $feedId) {
                    $query->where('feed_id', $feedId);
                })
                ->first();

            $this->feedContactId = $query ? $query->id : null;
            $this->feedContactIdStatus = $query ? $query->status : null;

            $phone2 = $this->phone2;

            $this->surveyContacts = CxTicket::where(function ($query) use ($phone, $phone2) {

                $query->where('customer_contact_01', $phone)
                    ->orWhere('customer_contact_02', $phone);

                if (!empty($phone2)) {
                    $query->orWhere('customer_contact_01', $phone2)
                        ->orWhere('customer_contact_02', $phone2);
                }
            })
                ->whereIn('status', ['Closed', 'Skip'])
                ->get();

            // $this->phone_numbers = [$phone];


            // if ($feedContacts->isNotEmpty()) {
            //     $allContacts = $feedContacts->flatMap(function ($contact) {
            //         return [$contact->contact_no_01, $contact->contact_no_02];
            //     });


            //     $normalized = $allContacts->map(function ($p) {
            //         $p = preg_replace('/\s+/', '', $p);
            //         if (strlen($p) === 10 && str_starts_with($p, '0')) {
            //             return substr($p, 1);
            //         }
            //         return $p;
            //     });


            //     $mainPhoneNormalized = preg_replace('/\s+/', '', $phone);
            //     if (strlen($mainPhoneNormalized) === 10 && str_starts_with($mainPhoneNormalized, '0')) {
            //         $mainPhoneNormalized = substr($mainPhoneNormalized, 1);
            //     }


            //     $this->phone_numbers = $normalized->push($mainPhoneNormalized)
            //         ->unique()
            //         ->values()
            //         ->all();
            // } else {

            //     $this->phone_numbers = [$phone];
            // }    





            // $this->surveyContacts = CxTicket::where(function ($query) {
            //     $query->whereIn('customer_contact_01', $this->phone_numbers)
            //         ->orWhereIn('customer_contact_02', $this->phone_numbers);
            // })
            //     ->whereIn('status', ['Closed', 'Skip'])
            //     ->get();


            // if ($this->surveyContacts->isNotEmpty()) {
            //     $foundContact = $this->surveyContacts->first();
            //     if ($foundContact->customer_contact_01 === $phone) {
            //         $this->phone2 = $foundContact->customer_contact_02;
            //     } else {
            //         $this->phone2 = $foundContact->customer_contact_01;
            //     }
            // } else {
            //     $this->phone2 = null;
            // }

            // Initialize array with main lead contact
            $this->phone_numbers = [$this->lead->contact_number];

            if ($this->surveyContacts->isNotEmpty()) {
                // Collect all customer_contact_01 and customer_contact_02 values
                $allContacts = $this->surveyContacts->flatMap(function ($contact) {
                    return [$contact->customer_contact_01, $contact->customer_contact_02];
                });

                // Normalize numbers: remove spaces, remove leading 0 if 10 digits
                $normalized = $allContacts->map(function ($p) {
                    $p = preg_replace('/\s+/', '', $p); // remove spaces
                    if (strlen($p) === 10 && str_starts_with($p, '0')) {
                        return substr($p, 1); // remove leading 0
                    }
                    return $p;
                });

                // Merge with lead contact and remove duplicates
                $leadPhoneNormalized = preg_replace('/\s+/', '', $this->lead->contact_number);
                if (strlen($leadPhoneNormalized) === 10 && str_starts_with($leadPhoneNormalized, '0')) {
                    $leadPhoneNormalized = substr($leadPhoneNormalized, 1);
                }

                $this->phone_numbers = $normalized->push($leadPhoneNormalized)
                    ->unique()
                    ->values()
                    ->all(); // final array of unique phone numbers
            }

        }
    }

    public function updated($propertyName)
    {
//         \Log::info('Property updated', ['property' => $propertyName, 'value' => $this->$propertyName]);

        if ($propertyName === 'attachment' && $this->attachment) {
            $this->notifyPhone = false;
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


    public function showWhatsAppModal()
    {
//         \Log::info('showWhatsAppModal called');
        $this->resetValidation();
        $this->whatsappMessage = '';
        $this->channelError = '';
        $this->notifyWhatsApp = !empty($this->lead->whatsapp);
        $this->notifyEmail = !empty($this->lead->email);
        $this->notifyPhone = !empty($this->lead->contact_number);
        $this->whatsappModal = true;
        $this->attachment = null;
        $this->attachmentResetKey++;
    }

    public function sendWhatsAppMessage()
    {
        if (!$this->notifyWhatsApp && !$this->notifyEmail && !$this->notifyPhone) {
            $this->channelError = 'Please select at least one notification channel.';
            return;
        }
        $this->channelError = '';

//         \Log::info('sendWhatsAppMessage called', ['message' => $this->whatsappMessage]);
        $this->validate([
            'whatsappMessage' => 'required|string',
        ]);

        $waSuccess = false;
        $emailSuccess = false;
        $waAttempted = false;
        $emailAttempted = false;
        $user = Auth::user()->name;
        $this->whatsappMessage = $this->whatsappMessage . "\n" . "<< " . $user . " >>" ;

        // --- WhatsApp Logic ---
        if ($this->notifyWhatsApp) {
            $waAttempted = true;
            $number = preg_replace('/\s+/', '', $this->lead->whatsapp);
//             \Log::info('WhatsApp Number from lead', ['number' => $number]);

            if (empty($number)) {
                $this->notification()->error('WhatsApp number is missing for this lead.');
            } else {
                if (str_starts_with($number, '94')) {
                    $internationalNumber = $number;
                } elseif (str_starts_with($number, '0')) {
                    $internationalNumber = '94' . substr($number, 1);
                } else {
                    $internationalNumber = '94' . $number;
                }

//                 \Log::info('International Number', ['number' => $internationalNumber]);
                $mode = config('services.whatsapp.mode', 'api');
                $response = null;

                if ($mode === 'webjs') {
                    $url = config('services.whatsapp.webjs_url') . '/send-message';
                    $postData = [
                        'to' => $internationalNumber,
                        'message' => $this->whatsappMessage,
                    ];

                    if ($this->attachment) {
                        try {
                            $path = $this->attachment->getRealPath();
                            $postData['attachment'] = [
                                'base64' => base64_encode(file_get_contents($path)),
                                'mimetype' => $this->attachment->getMimeType(),
                                'filename' => $this->attachment->getClientOriginalName(),
                            ];
                        } catch (\Exception $e) {
//                             \Log::error('File attachment error', ['error' => $e->getMessage()]);
                        }
                    }

                    try {
                        $response = Http::timeout(30)->post($url, $postData);
                    } catch (\Exception $e) {
//                         \Log::error('HTTP Exception', ['message' => $e->getMessage()]);
                        $this->notification()->error('Failed to connect to WhatsApp server: ' . $e->getMessage());
                    }
                } else {
                    // API Mode (Facebook Graph API)
                    // Note: Attachments not currently implemented for API mode in this block, 
                    // only text messages as per original code.
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . config('services.whatsapp.token'),
                        'Content-Type' => 'application/json',
                    ])->post('https://graph.facebook.com/v22.0/' . config('services.whatsapp.phone_id') . '/messages', [
                        'messaging_product' => 'whatsapp',
                        'to' => $internationalNumber,
                        'type' => 'template',
                        'template' => [
                            'name' => 'hello_world',
                            'language' => ['code' => 'en_US'],
                        ],
                    ]);
                }

                if ($response && $response->successful()) {
                    $waSuccess = true;
                } else {
                    $errorMsg = $response ? $response->body() : 'No response from server';
                    $this->notification()->error('Failed to send WhatsApp message: ' . $errorMsg);
                }
            }
        }

        // --- Email Logic ---
        if ($this->notifyEmail) {
            $emailAttempted = true;
            if (empty($this->lead->email)) {
                $this->notification()->error('Email address is missing for this lead.');
            } else {
                try {
                    $attachment = $this->attachment;
                    $messageContent = $this->whatsappMessage;
                    $toEmail = $this->lead->email;

                    Mail::raw($messageContent, function ($message) use ($toEmail, $attachment) {
                        $message->to($toEmail)
                            ->from(config('services.email.address'), config('services.email.name'))
                            // ->bcc('auso.info@gmail.com')
                            ->subject(config('services.email.subject')."_".Date('Y-m-d'));

                        if ($attachment) {
                            $message->attach($attachment->getRealPath(), [
                                'as' => $attachment->getClientOriginalName(),
                                'mime' => $attachment->getMimeType(),
                            ]);
                        }
                    });

                    $emailSuccess = true;
                } catch (\Exception $e) {
//                     \Log::error('Email sending failed', ['error' => $e->getMessage()]);
                    $this->notification()->error('Failed to send Email: ' . $e->getMessage());
                }
            }
        }

        // --- Phone Logic (Placeholder) ---
        if ($this->notifyPhone) {
            // Placeholder: Assume success for now as per original code
            $number = preg_replace('/\s+/', '', $this->lead->contact_number);
//             \Log::info('WhatsApp Number from lead', ['number' => $number]);

            if (empty($number)) {
                $this->notification()->error('WhatsApp number is missing for this lead.');
            } else {
                if (str_starts_with($number, '94')) {
                    $internationalNumber = $number;
                } elseif (str_starts_with($number, '0')) {
                    $internationalNumber = '94' . substr($number, 1);
                } else {
                    $internationalNumber = '94' . $number;
                }
            }

            $response = Http::withHeaders([
                        'Authorization' => 'Basic ' . config('services.mobile.token'),
                        'Content-Type' => 'application/json',
                        'Accept' => '*',
                        'X-API-VERSION' => 'v1',
                    ])->post(config('services.mobile.url'), [
                        'to' => $internationalNumber,
                        'text' => $this->whatsappMessage
                    ]);

            // $this->notification()->success('Phone notification initiated (simulated).');
        }

        // --- Final Result Handling ---
        $completed = true;
        if ($waAttempted && !$waSuccess) $completed = false;
        if ($emailAttempted && !$emailSuccess) $completed = false;

        if ($completed) {
            $this->notification()->success('Selected notifications sent successfully!');
            $this->whatsappModal = false;
            $this->whatsappMessage = '';
            $this->attachment = null;
        }
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

        $cmpName = '';
        if ($this->boundType && $this->boundType == 'dialer'){
            $cmpName = $this->campaign;
        }
        else{
            $cmpName = 'inbound';
        }

        CallbackCustomer::create([
            'agent_id' => auth()->id(),
            'lead_id' => $this->lead->id,
            'unique_id' => $this->lead->unique_id,
            'contact_number' => $this->lead->contact_number,
            'src' => 'lead',
            'callback_at' => Carbon::parse("{$this->callbackDate} {$this->callbackTime}"),
            'comment' => $this->callbackComment,
            'campaign' => $cmpName
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
