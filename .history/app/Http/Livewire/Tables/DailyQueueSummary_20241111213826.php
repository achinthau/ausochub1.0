<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyQueueSummery;  // Correct model used
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyQueueSummary extends LivewireDatatable
{
    public $model = DailyQueueSummery::class;

    public function builder()
    {
        // Return a query for fetching data from the daily_queue_summeries table
        return DailyQueueSummery::query();
    }

    public function columns()
    {
        return [
            // ID column (optional, for debugging purposes)
            NumberColumn::name('id')
                ->label('ID')
                ->linkTo('job', 6), // Customize the link as needed

            // Date column for daily summary
            DateColumn::name('date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            // Queue column
            Column::name('queue')
                ->label('Queue')
                ->searchable()
                ->filterable(),

            // Calls column (number of calls)
            NumberColumn::name('calls')
                ->label('Calls')
                ->sortable()
                ->filterable(),

            // Answered column (number of answered calls)
            NumberColumn::name('answered')
                ->label('Answered')
                ->sortable()
                ->filterable(),

            // Abandoned column (number of abandoned calls)
            NumberColumn::name('abandoned')
                ->label('Abandoned')
                ->sortable()
                ->filterable(),

            // Agents column (number of agents in the queue)
            NumberColumn::name('agents')
                ->label('Agents')
                ->sortable()
                ->filterable(),

            // Separate summary column for "Total Calls" (no duplication, summary applied here)
            NumberColumn::name('calls')
                ->label('Total Calls')
                ->enableSummary(),  // This enables a summary for the "calls" column (sum)

            // Optional: Add a custom label or computed column (e.g., call efficiency)
            Column::raw("ROUND((answered / calls) * 100, 2) AS call_efficiency")
                ->label('Call Efficiency (%)')
                ->sortable()
                ->filterable(),
        ];
    }
}
