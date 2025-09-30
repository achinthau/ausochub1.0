<?php

namespace App\Http\Livewire\Dialer\Dashboard\Admin;

use App\Models\Campaign;
use App\Models\CampaignMetric;
use App\Models\FeedContactValid;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $totalCampaigns;
    public $activeCampaigns;
    public $inactiveCampaigns;
    public $completedCampaigns;
    public $totalUsers;
    public $recentCampaigns;
    public $agents;
    public $campaigns;

    protected $listeners = ['changeStatus'];

    public function changeStatus($campaignId, $newStatus)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $campaign->status = $newStatus;
        $campaign->save();

        $this->campaigns = CampaignMetric::with('types')
            ->whereNotIn('status', [3, 4])
            ->get();

        $this->dispatchBrowserEvent('notify', 'Campaign status updated!');
    }
    public function mount()
    {
        // Load data for cards
        $this->totalCampaigns = Campaign::count();
        $this->activeCampaigns = Campaign::where('status', '1')->count();
        $this->inactiveCampaigns = Campaign::where('status', '0')->count();
        $this->completedCampaigns = Campaign::where('status', '3')->count();
        $this->totalUsers = User::count();
        $this->recentCampaigns = Campaign::orderBy('created_at', 'desc')->take(3)->get(['id', 'name', 'created_at']);
        $this->agents = User::whereNotNull('tenant_context')->orderBy('name')->take(5)->get(['id', 'name']);
        
    //    $this->campaigns = Campaign::with('types')->where('status', 'inactive')->get();

    //     $this->campaigns->each(function ($campaign) {
    //         $feedIds = $campaign->feed_ids; // Use accessor

    //         // Total unique contacts for assigned feeds
    //         $campaign->contact_count = $feedIds
    //             ? FeedContactValid::whereIn('feed_id', $feedIds)
    //                 ->distinct('phone')
    //                 ->count('phone')
    //             : 0;

    //         // Dialed count (status is not null)
    //         $campaign->dialed_count = $feedIds
    //             ? FeedContactValid::whereIn('feed_id', $feedIds)
    //                 ->whereNotNull('status')
    //                 ->distinct('phone')
    //                 ->count('phone')
    //             : 0;

    //         // Answered count (status = 'answered')
    //         $campaign->answered_count = $feedIds
    //             ? FeedContactValid::whereIn('feed_id', $feedIds)
    //                 ->where('status', 'answered')
    //                 ->distinct('phone')
    //                 ->count('phone')
    //             : 0;

    //         // Agents count from assigned_users
    //         $campaign->agents_count = $campaign->assigned_users
    //             ? count(array_filter(explode(',', $campaign->assigned_users)))
    //             : 0;
    //     });

    $this->campaigns = CampaignMetric::with('types')
            ->whereNotIn('status', [3, 4])
            ->get();
    }
    public function render()
    {
        return view('livewire.dialer.dashboard.admin.index');
    }
}
