<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Builder;

class CampaignTable extends DataTableComponent
{
    protected $model = Campaign::class;

    protected $listeners = ['campaignTableUpdated' => 'refreshTable'];

    public function refreshTable()
    {
        $this->resetPage();
    }
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
{
    return Campaign::query()->with('creator')->with('companies');
}

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            // Assigned users
        Column::make("Assigned Users", "assigned_users")
            ->format(fn($value, $row) => 
                implode(', ', \App\Models\User::whereIn('id', explode(',', $row->assigned_users))->pluck('name')->toArray())
            ),

        // Assigned feeds
        Column::make("Assigned Feeds", "assigned_feeds")
            ->format(fn($value, $row) => 
                implode(', ', \App\Models\Feed::whereIn('id', explode(',', $row->assigned_feeds))->pluck('name')->toArray())
            ),
            Column::make("Company")
            ->format(fn($value, $row) => $row->companies?->name ?? 'N/A'),
                Column::make("Created by")
            ->format(fn($value, $row) => $row->creator?->name ?? 'N/A'),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make("Scheduled time", "schedule")
                ->sortable(),
                Column::make("Actions")
                ->label(fn($row) => view('livewire.dialer.settings.campaign.table-action', ['feed' => $row]))
                ->html(),
        ];
    }
}
