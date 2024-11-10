<?php

namespace App\Http\Livewire;

use App\Exports\TicketExport;
use App\Models\Outlet;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Mediconesystems\LivewireDatatables\DateColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class NewTicketTable extends DataTableComponent
{
    protected $model = Ticket::class;

    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }


    public function export()
    {
        $tickets = $this->getSelected();

        $this->clearSelected();
        $timestamp = Carbon::now()->timestamp;
        return Excel::download(new TicketExport($tickets), 'tickets_' . $timestamp . '.xlsx');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setQueryStringDisabled()->setBulkActionsStatus(Gate::allows('can-export-ticket'))->setDefaultSort('updated_at', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()->searchable(),
            Column::make("Topic", "topic")
                ->sortable()->searchable(),
            Column::make("Description", "description")
                ->sortable()->searchable(),
            Column::make("Contact", "lead.contact_number")
                ->sortable()->searchable(),
            Column::make("Category", "category.title")
                ->sortable(),
            Column::make("Sub Category", "subCategory.title")
                ->sortable(),

            Column::make("Tags", "tags")->format(function ($value) {
                $displayValue = is_array($value) ? implode(', ', $value) : $value;
                return $displayValue;
            })->sortable()->deselected(),
            Column::make("Outlet", "outlet.title")
                ->sortable()->deselected(),
            Column::make("Due At", "due_at")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable()->deselected(),
            Column::make("Updated at", "updated_at")
                ->sortable()->deselected(),
            Column::make("Ticket Status Id", "ticket_status_id")
                ->sortable()->hideIf(true),
            Column::make("Status")
                ->label(function ($row) {
                    return view('livewire.ticket-items.status', ['ticketStatusId' => $row->ticket_status_id, 'ticketItemId' => $row->id, 'status' => $row]);
                })
                ->html(),

        ];
    }


    public function filters(): array
    {
        return [
            MultiSelectFilter::make('Status')
                ->options(
                    TicketStatus::query()
                        ->orderBy('title')
                        ->get()
                        ->keyBy('id')
                        ->map(fn($tag) => $tag->title)
                        ->toArray()
                )->filter(function (Builder $builder, array $values) {
                    $builder->whereHas('status', fn($query) => $query->whereIn('ticket_statuses.id', $values));
                }),
            MultiSelectFilter::make('Category')
                ->options(
                    TicketCategory::query()
                        ->orderBy('title')
                        ->get()
                        ->keyBy('id')
                        ->map(fn($tag) => $tag->title)
                        ->toArray()
                )->filter(function (Builder $builder, array $values) {
                    $builder->whereHas('category', fn($query) => $query->whereIn('ticket_categories.id', $values));
                }),
            MultiSelectFilter::make('Sub Category')
                ->options(
                    TicketCategory::query()
                        ->orderBy('title')
                        ->get()
                        ->keyBy('id')
                        ->map(fn($tag) => $tag->title)
                        ->toArray()
                )->filter(function (Builder $builder, array $values) {
                    $builder->whereHas('subCategory', fn($query) => $query->whereIn('ticket_sub_categories.id', $values));
                }),
            SelectFilter::make('Outlet')
                ->options(
                    Outlet::query()
                        ->orderBy('title')
                        ->get()
                        ->keyBy('id')
                        ->map(fn($tag) => $tag->title)
                        ->toArray()
                )->filter(function (Builder $builder,  $value) {
                    $builder->whereHas('outlet', fn($query) => $query->where('outlets.id', $value));
                }),
            DateFilter::make('Due From')
                ->config([
                    // 'min' => '2020-01-01',
                    // 'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('due_at', '>=', $value);
                }),
            DateFilter::make('Due To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('due_at', '<=', $value);
                })

        ];
    }

    public function openModal($id)
    {
        $this->emitTo('tickets.show', 'openTicket', $id);
    }

    public function builder(): Builder
    {
        return Ticket::with('status');
    }
}
