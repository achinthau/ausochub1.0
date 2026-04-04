<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\AgentPerformance;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Illuminate\Support\HtmlString;
use App\Exports\AgentPerformanceExport;
use Maatwebsite\Excel\Facades\Excel;



class CallAgentPerformanceTable extends DataTableComponent
{
    protected $model = AgentPerformance::class;
    public $exportable = true;
    public $hideable = 'select';

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('date', 'desc')
            ->setDefaultSort('agent_name', 'asc')
            ->setPerPage(10)
            ->setPageName('page');
    }

    public function builder(): Builder
    {
        return AgentPerformance::query()
            ->select(
                'id', 'date', 'agent_id', 'agent_name', 'extension',
                'total_calls', 'avg_calls', 'total_missed', 'avg_missed',
                'acw', 'avg_acw', 'other_break', 'avg_oth_break',
                'active_time', 'talk_time', 'avg_talk_time',
                'tickets_created_count', 'queues', 'created_at', 'updated_at'
            );
    }

    public function columns(): array
    {
        return [
            Column::make("Date", "date")
                ->sortable()
                ->searchable()
                ->format(fn ($value) => $value?->format('Y-m-d')),

            Column::make("Agent", "agent_name")
                ->sortable()
                ->searchable(),

            Column::make("Ext", "extension")
                ->sortable()
                ->searchable(),

            Column::make("Calls", "total_calls")
                ->sortable()
                ->format(fn ($value) => number_format($value)),

            Column::make("Avg Calls", "avg_calls")
                ->sortable()
                ->format(fn ($value) => number_format($value, 2)),

            Column::make("Missed", "total_missed")
                ->sortable()
                ->format(fn ($value) => number_format($value)),

            Column::make("Avg Missed", "avg_missed")
                ->sortable()
                ->format(fn ($value) => number_format($value, 2)),

            Column::make("ACW (m)", "acw")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Avg ACW (m)", "avg_acw")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Break (m)", "other_break")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Avg Break (m)", "avg_oth_break")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Active (m)", "active_time")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Talk (m)", "talk_time")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Avg Talk (m)", "avg_talk_time")
                ->sortable()
                ->format(fn ($value) => number_format($value / 60, 2)),

            Column::make("Tickets", "tickets_created_count")
                ->sortable()
                ->format(fn ($value) => number_format($value)),

            Column::make("Queues", "queues")
                ->format(function ($value) {
                    if (!$value) {
                        return '-';
                    }
                    
                    // Handle both string and array types
                    if (is_string($value)) {
                        $queues = json_decode($value, true);
                    } else {
                        $queues = $value;
                    }
                    
                    if (!is_array($queues) || empty($queues)) {
                        return '-';
                    }
                    
                    $badges = array_map(
                        fn ($q) => '<span class="inline-flex items-center px-2 py-1 text-xs font-semibold leading-tight text-blue-600 bg-blue-100 rounded-full">' 
                            . htmlspecialchars($q['name']) . ': ' . $q['call_count'] 
                            . '</span>',
                        $queues
                    );
                    return new HtmlString(implode(' ', $badges));
                }),
        ];
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Date')
                ->config([
                    'placeholder' => 'Filter by date',
                    'altFormat' => 'F j, Y',
                    'dateFormat' => 'Y-m-d',
                ])
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereDate('date', $value);
                }),
            
            TextFilter::make('Agent')
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('agent_name', 'like', '%' . $value . '%');
                }),
            
            TextFilter::make('Extension')
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('extension', 'like', '%' . $value . '%');
                }),
        ];
    }


    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }

    public function export()
    {
        // Get selected record IDs
        $selectedIds = $this->getSelected();
        
        // Log for debugging
        \Log::info('Export: selectedIds', ['ids' => $selectedIds]);

        // Ensure selectedIds is an array
        if (!is_array($selectedIds) || empty($selectedIds)) {
            session()->flash('error', 'No records selected for export.');
            return;
        }

        // Remove empty values and cast to integers
        $selectedIds = array_filter(
            array_map(fn($id) => is_numeric($id) && $id !== '' ? (int)$id : null, $selectedIds),
            fn($id) => $id !== null
        );

        \Log::info('Export: after filter', ['ids' => $selectedIds]);

        if (empty($selectedIds)) {
            session()->flash('error', 'Invalid record IDs.');
            return;
        }

        // Fetch records using the same connection as the model
        $records = AgentPerformance::whereIn('id', $selectedIds)->get();

        \Log::info('Export: fetched records', ['count' => $records->count()]);

        if ($records->isEmpty()) {
            session()->flash('error', 'No records found.');
            return;
        }

        // Clear selection
        $this->clearSelected();

        // Download Excel file
        return Excel::download(
            new AgentPerformanceExport($records), 
            'agent_performance_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
}
