<?php

namespace App\Http\Livewire\Dialer\Settings\Campaign;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
    public $user_ids = [];
    public $feed_ids = [];
    public $companies = [];
    public $feeds = [];
    public $users;

    public function mount($campaign = null)
    {
        $this->reset(['campaignId', 'name', 'company_id', 'user_ids', 'feed_ids', 'users']);
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
        $this->feeds = Feed::orderBy('name')->get(['id', 'name']);
        $this->users = collect();

        if ($campaign) {
            $this->campaignId = $campaign->id;
            $this->name = $campaign->name;
            $this->company_id = $campaign->company;
            $this->user_ids = $campaign->assigned_users
                ? array_map('intval', explode(',', trim($campaign->assigned_users)))
                : [];
            $this->feed_ids = $campaign->assigned_feeds
                ? array_map('intval', array_filter(explode(',', trim($campaign->assigned_feeds))))
                : [];
            $this->updatedCompanyId($this->company_id);
        }
    }

    public function showCreateCampaignModal()
    {
        $this->reset(['campaignId', 'name', 'company_id', 'user_ids', 'feed_ids', 'users']);
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
        $this->feeds = Feed::orderBy('name')->get(['id', 'name']);
        $this->users = collect();
        $this->createCampaignModal = true;
        $this->dispatchBrowserEvent('refresh-modal');

        \Log::debug('Create Campaign Modal Opened', [
            'campaign_id' => $this->campaignId,
            'name' => $this->name,
            'company_id' => $this->company_id,
            'user_ids' => $this->user_ids,
            'feed_ids' => $this->feed_ids,
            'users' => $this->users->toArray(),
            'feeds' => $this->feeds->toArray(),
        ]);
    }

    public function showUpdateCampaignModal($campaign_id)
    {
        $this->reset(['user_ids', 'feed_ids', 'users', 'name', 'company_id', 'campaignId']);
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
        $this->feeds = Feed::orderBy('name')->get(['id', 'name']);
        $this->users = collect();

        $this->campaign = Campaign::find($campaign_id);
        if ($this->campaign) {
            $this->campaignId = $this->campaign->id;
            $this->name = $this->campaign->name;
            $this->company_id = $this->campaign->company;
            $this->user_ids = $this->campaign->assigned_users
                ? array_map('intval', array_filter(explode(',', trim($this->campaign->assigned_users))))
                : [];
            $this->feed_ids = $this->campaign->assigned_feeds
                ? array_map('intval', array_filter(explode(',', trim($this->campaign->assigned_feeds))))
                : [];
            $this->updatedCompanyId($this->company_id);

            \Log::debug('Campaign Data', [
                'campaign_id' => $this->campaignId,
                'name' => $this->name,
                'company_id' => $this->company_id,
                'user_ids' => $this->user_ids,
                'feed_ids' => $this->feed_ids,
                'users' => $this->users->toArray(),
                'feeds' => $this->feeds->toArray(),
                'raw_assigned_users' => $this->campaign->assigned_users,
                'raw_assigned_feeds' => $this->campaign->assigned_feeds,
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
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:ac_companies,id',
            'user_ids' => 'array|exists:ac_users,id',
            'feed_ids' => 'array|exists:ac_feeds,id',
        ]);

        $data = [
            'name' => $this->name,
            'company' => $this->company_id,
            'assigned_users' => !empty($this->user_ids) ? implode(',', $this->user_ids) : null,
            'assigned_feeds' => !empty($this->feed_ids) ? implode(',', $this->feed_ids) : null,
        ];

        if ($this->campaignId) {
            $campaign = Campaign::findOrFail($this->campaignId);
            $campaign->update($data);
        } else {
            $data['status'] = 'inactive';
            $data['created_by'] = Auth::user()->id;
            Campaign::create($data);
        }

        $this->createCampaignModal = false;
        $this->emit('campaignTableUpdated');
    }

    public function render()
    {
        return view('livewire.dialer.settings.campaign.create');
    }
}