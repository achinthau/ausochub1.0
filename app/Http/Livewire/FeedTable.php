<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Feed;

class FeedTable extends DataTableComponent
{
    protected $model = Feed::class;

    protected $listeners = ['feedTableUpdated' => 'refreshTable'];

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
    return Feed::query()->with('uploader');
}


    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Description", "description")
                ->sortable(),
            Column::make("Uploaded by")
            ->format(fn($value, $row) => $row->uploader?->name ?? 'N/A'),
            Column::make("File name", "file_name")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make("Actions")
                ->label(fn($row) => view('livewire.dialer.settings.feed.table-action', ['feed' => $row]))
                ->html(),
        ];
    }
}
