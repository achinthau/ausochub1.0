<?php

namespace App\Http\Livewire\Reports;

use App\Http\Livewire\CallAgentPerformanceTable;
use Livewire\Component;


class AgentPerformanceMetricsReport extends Component
{
    public function render()
    {
        return view('livewire.reports.agent-performance-metrics-report');
    }
}
