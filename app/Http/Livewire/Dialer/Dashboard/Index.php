<?php

namespace App\Http\Livewire\Dialer\Dashboard;
use App\Models\Campaign;
use App\Models\CampaignMetric;
use App\Models\FeedContactValid;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Livewire\Component;

class Index extends Component
{

    public $totalAssignedCampaigns;
    public $activeAssignedCampaigns;
    public $totalCallsToday;
    public $answeredCallsToday;
    public $campaigns;
    public $agents;

    public function mount()
    {
        $userId = Auth::id();

        // Load metrics for cards
        $this->totalAssignedCampaigns = CampaignMetric::whereRaw('FIND_IN_SET(?, assigned_users)', [$userId])
            ->count();
        $this->activeAssignedCampaigns = CampaignMetric::whereRaw('FIND_IN_SET(?, assigned_users)', [$userId])
            ->where('status', 'active')
            ->count();
        $this->totalCallsToday = FeedContactValid::whereIn('feed_id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('feeds')
                    ->whereIn('id', function ($subQuery) use ($userId) {
                        $subQuery->selectRaw('TRIM(BOTH "," FROM assigned_feeds)')
                            ->from('campaigns')
                            ->whereRaw('FIND_IN_SET(?, assigned_users)', [$userId]);
                    });
            })
            ->whereNotNull('status')
            ->whereDate('updated_at', today())
            ->distinct('phone')
            ->count('phone');
        $this->answeredCallsToday = FeedContactValid::whereIn('feed_id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('feeds')
                    ->whereIn('id', function ($subQuery) use ($userId) {
                        $subQuery->selectRaw('TRIM(BOTH "," FROM assigned_feeds)')
                            ->from('campaigns')
                            ->whereRaw('FIND_IN_SET(?, assigned_users)', [$userId]);
                    });
            })
            ->where('status', 'answered')
            ->whereDate('updated_at', today())
            ->distinct('phone')
            ->count('phone');

        // Load assigned campaigns
        $this->campaigns = CampaignMetric::with('types')
            ->whereRaw('FIND_IN_SET(?, assigned_users)', [$userId])
            ->get();

        // Load all agents (same as Supervisor Dashboard)
        $this->agents = User::whereNotNull('tenant_context')
            ->orderBy('name')
            ->take(5)
            ->get(['id', 'name']);
    }
    public function render()
    {
        return view('livewire.dialer.dashboard.index');
    }
}
