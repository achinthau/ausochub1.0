<?php

namespace App\Http\Livewire\Dialer\Settings\Campaign;

use App\Models\AgentSkill;
use App\Models\Campaign;
use App\Models\CampaignType;
use App\Models\Company;
use App\Models\Feed;
use App\Models\Skill;
use App\Models\User;
use App\Repositories\ApiManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Create extends Component
{
    protected $listeners = [
        'showUpdateCampaignModal' => 'showUpdateCampaignModal',
        'showCreateCampaignModal' => 'showCreateCampaignModal',
    ];

    public $campaignId = null;
    public $createCampaignModal = false;
    public $name;
    public $company_id;
    public $campaign_type_id;
    public $user_ids = [];
    public $feed_ids = [];
    public $companies = [];
    public $campaignTypes = [];
    public $feeds = [];
    public $users;
    public $status;
    public $service_type;
    public $hotline;

    public $schedule = [
        'monday' => ['start' => '', 'end' => ''],
        'tuesday' => ['start' => '', 'end' => ''],
        'wednesday' => ['start' => '', 'end' => ''],
        'thursday' => ['start' => '', 'end' => ''],
        'friday' => ['start' => '', 'end' => ''],
        'saturday' => ['start' => '', 'end' => ''],
        'sunday' => ['start' => '', 'end' => ''],
    ];
    public $savedSchedule = '';

    public function mount($campaign = null)
    {
        $this->reset(['campaignId', 'name', 'company_id', 'user_ids', 'feed_ids', 'users', 'schedule']);
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
        $this->campaignTypes = CampaignType::orderBy('name')->get(['id', 'name']);
        $this->feeds = Feed::orderBy('name')->get(['id', 'name']);
        $this->users = collect();

        if ($campaign) {
            $this->loadCampaign($campaign);
        }

        $this->savedSchedule = json_encode($this->formatSchedule(), JSON_PRETTY_PRINT);
    }

    private function loadCampaign($campaign)
    {
        $this->campaignId = $campaign->id;
        $this->name = $campaign->name;
        $this->name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $campaign->name);
        $this->name = ucwords($this->name);
        $this->company_id = $campaign->company;
        $this->campaign_type_id = $campaign->type;
        $this->status = $campaign->status;
        $this->service_type = $campaign->service_type;
        $this->hotline = $campaign->hotline;
        $this->user_ids = $campaign->assigned_users
            ? array_map('intval', array_filter(explode(',', trim($campaign->assigned_users))))
            : [];
        $this->feed_ids = $campaign->assigned_feeds
            ? array_map('intval', array_filter(explode(',', trim($campaign->assigned_feeds))))
            : [];
        // Load schedule
        if ($campaign->schedule) {
            $scheduleData = json_decode($campaign->schedule, true);
            foreach ($this->schedule as $day => &$times) {
                if (isset($scheduleData[$day])) {
                    [$start, $end] = explode('-', $scheduleData[$day]);
                    $times['start'] = trim($start);
                    $times['end'] = trim($end);
                }
            }
        }
        $this->updatedCompanyId($this->company_id);
    }

    public function showCreateCampaignModal()
    {
        $this->reset(['campaignId', 'name', 'company_id', 'user_ids', 'feed_ids', 'users', 'schedule']);
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
        $this->campaignTypes = CampaignType::orderBy('name')->get(['id', 'name']);
        $this->feeds = Feed::orderBy('name')->get(['id', 'name']);
        $this->users = collect();
        $this->createCampaignModal = true;
        $this->savedSchedule = json_encode($this->formatSchedule(), JSON_PRETTY_PRINT);
        $this->dispatchBrowserEvent('refresh-modal');

        \Log::debug('Create Campaign Modal Opened', [
            'campaign_id' => $this->campaignId,
            'name' => $this->name,
            'company_id' => $this->company_id,
            'user_ids' => $this->user_ids,
            'feed_ids' => $this->feed_ids,
            'users' => $this->users->toArray(),
            'feeds' => $this->feeds->toArray(),
            'schedule' => $this->schedule,
        ]);
    }

    public function showUpdateCampaignModal($campaign_id)
    {
        $this->reset(['campaignId', 'name', 'company_id', 'user_ids', 'feed_ids', 'users', 'schedule']);
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
        $this->campaignTypes = CampaignType::orderBy('name')->get(['id', 'name']);
        $this->feeds = Feed::orderBy('name')->get(['id', 'name']);
        $this->users = collect();


        $campaign = Campaign::find($campaign_id);
        if ($campaign) {
            $this->loadCampaign($campaign);
            \Log::debug('Campaign Data Loaded', [
                'campaign_id' => $this->campaignId,
                'name' => $this->name,
                'company_id' => $this->company_id,
                'user_ids' => $this->user_ids,
                'feed_ids' => $this->feed_ids,
                'users' => $this->users->toArray(),
                'feeds' => $this->feeds->toArray(),
                'schedule' => $this->schedule,
                'raw_assigned_users' => $campaign->assigned_users,
                'raw_assigned_feeds' => $campaign->assigned_feeds,
                'raw_schedule' => $campaign->schedule,
            ]);
        } else {
            \Log::error('Campaign not found', ['campaign_id' => $campaign_id]);
        }
        $this->createCampaignModal = true;
        $this->dispatchBrowserEvent('refresh-modal');
    }

    public function updatedCompanyId($value)
    {
        if (!$value) {
            $this->users = collect();
            $this->user_ids = [];
            \Log::debug('No company selected', ['company_id' => $value]);
            return;
        }

        $company = Company::find($value);
        if (!$company) {
            $this->users = collect();
            $this->user_ids = [];
            \Log::error('Company not found', ['company_id' => $value]);
            return;
        }

        $companyName = strtolower(str_replace(' ', '', trim($company->name)));

        $this->users = User::query()
            ->where('user_type_id', '4')
            ->whereNotNull('tenant_context')
            ->whereRaw(
                "FIND_IN_SET(?, LOWER(REPLACE(tenant_context, ' ', '')))",
                [$companyName]
            )
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($this->campaignId) {
            $campaign = Campaign::find($this->campaignId);
            if ($campaign && $campaign->company == $value) {
                $this->user_ids = $campaign->assigned_users
                    ? array_map('intval', array_filter(explode(',', trim($campaign->assigned_users))))
                    : [];
                $valid_user_ids = $this->users->pluck('id')->toArray();
                $this->user_ids = array_filter($this->user_ids, fn($id) => in_array($id, $valid_user_ids));
            } else {
                $this->user_ids = [];
            }
        } else {
            $this->user_ids = [];
        }

        \Log::debug('Users fetched for company', [
            'company_id' => $value,
            'company_name' => $companyName,
            'users' => $this->users->toArray(),
            'user_ids' => $this->user_ids,
        ]);
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255|unique:campaigns,name',
            //     'company_id' => 'required|exists:companies,id',
            //     'user_ids' => 'array|exists:users,id',
            //     'feed_ids' => 'array|exists:feeds,id',
            //     'schedule.monday.start' => 'nullable|date_format:h:i A',
            //     'schedule.monday.end' => 'nullable|date_format:h:i A|after:schedule.monday.start',
            //     'schedule.tuesday.start' => 'nullable|date_format:h:i A',
            //     'schedule.tuesday.end' => 'nullable|date_format:h:i A|after:schedule.tuesday.start',
            //     'schedule.wednesday.start' => 'nullable|date_format:h:i A',
            //     'schedule.wednesday.end' => 'nullable|date_format:h:i A|after:schedule.wednesday.start',
            //     'schedule.thursday.start' => 'nullable|date_format:h:i A',
            //     'schedule.thursday.end' => 'nullable|date_format:h:i A|after:schedule.thursday.start',
            //     'schedule.friday.start' => 'nullable|date_format:h:i A',
            //     'schedule.friday.end' => 'nullable|date_format:h:i A|after:schedule.friday.start',
            //     'schedule.saturday.start' => 'nullable|date_format:h:i A',
            //     'schedule.saturday.end' => 'nullable|date_format:h:i A|after:schedule.saturday.start',
            //     'schedule.sunday.start' => 'nullable|date_format:h:i A',
            //     'schedule.sunday.end' => 'nullable|date_format:h:i A|after:schedule.sunday.start',
        ]);

        $formattedSchedule = $this->formatSchedule();
        $this->savedSchedule = json_encode($formattedSchedule, JSON_PRETTY_PRINT);
        $this->name = ucwords($this->name);
        $this->name = str_replace(' ', '', $this->name);
        $this->name = lcfirst($this->name);

        $data = [
            'name' => $this->name,
            'company' => $this->company_id,
            'type' => $this->campaign_type_id,
            'service_type' => $this->service_type,
            'hotline' => $this->hotline,
            'assigned_users' => !empty($this->user_ids) ? implode(',', $this->user_ids) : null,
            'assigned_feeds' => !empty($this->feed_ids) ? implode(',', $this->feed_ids) : null,
            'schedule' => $this->savedSchedule,
        ];

        if ($this->campaignId) {
            $data['status'] = $this->status;
            $campaign = Campaign::findOrFail($this->campaignId);
            $campaign->update($data);
            \Log::debug('Campaign Updated', ['data' => $data]);
        } else {
            $data['status'] = '0';
            $data['created_by'] = Auth::user()->id;
            Campaign::create($data);
            \Log::debug('Campaign Created', ['data' => $data]);




            $data2 = [
                ['name' => 'queueName', 'contents' => $this->name],
                ['name' => 'mohClass', 'contents' => 'silence'],
                ['name' => 'type', 'contents' => 'dialer'],
            ];

            $response = ApiManager::createSkill($data2);
            sleep(2);

            $skill = Skill::where('skillname', $this->name)->first();
            $userIds = $this->user_ids;

            foreach ($userIds as $userId) {
                $user = User::with('agent')->find($userId);

                if ($user && $user->agent) {
                    $agentId = $user->agent->id;
                } else {
                    $agentId = null;
                }
                $agentSkill = AgentSkill::firstOrCreate(
                    ['agentid' => $agentId],
                    ['skills' => '', 'dialer_skill_ids' => []]
                );


                $existingDialer = $agentSkill->dialer_skill_ids ?? [];
                $existingSkills = $agentSkill->skills ? explode(',', $agentSkill->skills) : [];


                $existingDialer[$skill->skillid] = $skill->skillname;
                if (!in_array($skill->skillname, $existingSkills)) {
                    $existingSkills[] = $skill->skillname;
                }


                $agentSkill->type = 'dialer';
                $agentSkill->dialer_skill_ids = $existingDialer;
                $agentSkill->skills = implode(',', $existingSkills);
                $agentSkill->save();
            }

        }



        $this->createCampaignModal = false;
        $this->emit('campaignTableUpdated');
    }

    //status
    //['0'=>'inactive'] not started
    //['1'=>'active'] running
    //['2'=>'hold']
    //['3'=>'completed']
    //['4'=>'canceled']

    private function formatSchedule()
    {
        $formatted = [];
        foreach ($this->schedule as $day => $times) {
            if (!empty($times['start']) && !empty($times['end'])) {
                $formatted[$day] = sprintf('%s-%s', $times['start'], $times['end']);
            }
        }
        return $formatted;
    }

    public function render()
    {
        return view('livewire.dialer.settings.campaign.create');
    }
}