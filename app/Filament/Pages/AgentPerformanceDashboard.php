<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AgentPerformanceDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $slug = 'agent-performance-dashboard';
    protected static ?string $title = 'Agent Performance Dashboard';

    protected static string $view = 'filament.pages.agent-performance-dashboard';

    public $agents = [];
    public $campaigns = [];
    public $startDate;
    public $endDate;
    public $mode = 'inbound'; // inbound | outbound
    public $campaignId = '';

    protected $queryString = [
        'startDate',
        'endDate',
        'mode',
        'campaignId',
    ];

    public function mount()
    {
        if (! auth()->user()->can('is-admin')) {
            abort(403);
        }
        
        $this->startDate = request()->query('startDate', now()->toDateString());
        $this->endDate = request()->query('endDate', now()->toDateString());
        $this->mode = request()->query('mode', 'inbound');
        $this->campaignId = request()->query('campaignId', '');
        
        $user = auth()->user();
        $companyId = $user->tenant_context;
        
        // Fetch Campaigns
        // tenant_context is a comma-separated list of company names, not IDs
        $userTenants = array_map('trim', explode(',', $companyId));
        $tenantIds = \App\Models\Company::whereIn('name', $userTenants)->pluck('id');
        
        $this->campaigns = \App\Models\Campaign::whereIn('company', $tenantIds)->pluck('name', 'id');

        // Fetch Agents
        $agentIds = [];
        
        // If Outbound mode and Campaign selected, filter agents by campaign assignment
        if ($this->mode === 'outbound' && !empty($this->campaignId)) {
            $campaign = \App\Models\Campaign::find($this->campaignId);
            if ($campaign && $campaign->assigned_users) {
                // assigned_users is comma separated User IDs
                $userIds = array_map('trim', explode(',', $campaign->assigned_users));
                // Map Users to Agents
                $agentIds = \App\Models\User::whereIn('id', $userIds)->pluck('agent_id')->filter()->toArray();
            }
        } else {
            // Default: All agents of the tenant
            $agentIds = \App\Models\User::where('tenant_context', $companyId)->pluck('agent_id')->filter()->toArray();
        }

        if (!empty($agentIds)) {
            $this->agents = \App\Models\Agent::whereIn('id', $agentIds)
                ->whereNotNull('extension')
                ->get();
        } else {
             $this->agents = collect();
        }
    }

    protected function getViewData(): array
    {
        return [
            'agents' => $this->agents,
            'campaigns' => $this->campaigns,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'mode' => $this->mode,
            'campaignId' => $this->campaignId,
        ];
    }
}
