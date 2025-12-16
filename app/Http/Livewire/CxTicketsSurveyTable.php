<?php

namespace App\Http\Livewire;

use App\Exports\CxTicketsSurveyExport;
use App\Models\CxTicketCategory;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\CxTicket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class CxTicketsSurveyTable extends DataTableComponent
{
    protected $model = CxTicket::class;

    // Set the default search value in the mount method
    // public function mount()
    // {
    //     // Set a default value for search
    //     $this->search = ''; // Clear the default value to allow for dynamic searching
    // }

    protected $listeners = ['cxTicketSurveyUpdated' => 'refreshTable'];

    public function refreshTable()
    {
        $this->resetPage();
    }


    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }

    public function export()
{
    $selectedIds = $this->getSelected();

    $tickets = CxTicket::whereIn('id', $selectedIds)->get();
    return Excel::download(new CxTicketsSurveyExport($tickets), 'cx_tickets_completed.xlsx');
}


    public function configure(): void
    {
        $this->setPrimaryKey('id');

        // $this->setPaginationVisibilityDisabled();
        // $this->setPerPageVisibilityDisabled();
        // $this->setPerPageAccepted([10, 25, 50, 100]);
        // $this->setPerPage(10);
    }


//     public function builder(): Builder
// {
//     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));

//     return CxTicket::query()
//     ->where('status', 'Closed')
//         ->whereIn('company', $companyNames)
//         ->orderBy('updated_at', 'asc');
// }

public function builder(): Builder
{
    $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));

    return CxTicket::query()
        ->whereIn('company', $companyNames)
        ->where(function ($q) {
            $q->where('status', 'Closed')
        ->orWhere('status', 'Remind')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'Skip')
                     ->whereDate('updated_at', '!=', now()->toDateString());
              });
        })
        ->orderBy('updated_at', 'asc');
}


    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Category", "category")
                ->sortable(),
            Column::make("Product", "product")
                ->sortable(),
            Column::make("Model", "model")
                ->sortable(),
            Column::make("Work order no", "work_order_no")
                ->sortable()
                ->searchable(),
            Column::make("Service center", "service_center")
                ->sortable(),
            Column::make("Warranty status", "warranty_status")
                ->sortable(),
            Column::make("Sold date", "sold_date")
                ->sortable(),
            Column::make("Customer name", "customer_name")
                ->sortable(),
            Column::make("Customer address", "customer_address")
                ->sortable(),
            Column::make("Customer contact 01", "customer_contact_01")
                ->sortable()
                ->searchable(),
            Column::make("Customer contact 02", "customer_contact_02")
                ->sortable()
                ->searchable(),
            Column::make("Technician name", "technician_name")
                ->sortable(),
            Column::make("Technician contact", "technician_contact")
                ->sortable(),
            Column::make("Supervisor name", "supervisor_name")
                ->sortable(),
            Column::make("Supervisor contact", "supervisor_contact")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            Column::make("Creator", "creator")
                ->sortable(),
            Column::make("Change Request", "change_request")->sortable()->searchable(), 
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make("Actions")
                ->label(fn($row) => view('livewire.cx-tickets.survey.rating-button', ['ticket' => $row]))
                ->html(),
        ];
    }

    public function filters(): array
    {
        $categories = CxTicketCategory::pluck('name', 'name')->toArray();

        $options = ['' => 'All'] + $categories;


        return [

            SelectFilter::make('Category')
                ->options($options)
                ->filter(function ($query, $value) {
                    if ($value !== '') {
                        $query->where('category', $value);
                    }
                }),

            SelectFilter::make('Change Request')
                ->options([
                    '' => 'All',
                    'has' => 'Yes',
                    'null' => 'No',
                ])
                ->filter(function (Builder $query, string $value) {
                    if ($value === 'has') {
                        $query->whereNotNull('change_request')
                            ->where('change_request', '!=', '');
                    }

                    if ($value === 'null') {
                        $query->where(function ($q) {
                            $q->whereNull('change_request')
                                ->orWhere('change_request', '');
                        });
                    }
                }),





            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    'Open' => 'Pending',
                    'Closed' => 'Completed',
                    'Rated' => 'Rated',
                    'Canceled' => 'Canceled',
                    'ReOpened' => 'ReOpened',
                    'Satisfied' => 'Satisfied',
                    'Unsatisfied' => 'Unsatisfied',
                    // 'Neutral' => 'Neutral', 
                    'Passive' => 'Passive',
                ])
                ->filter(function ($query, $value) {
                    if ($value === 'Satisfied') {
                        $query->where('status', 'Rated')
                            ->where('satisfaction_rate', '>', 3);
                    } elseif ($value === 'Unsatisfied') {
                        $query->where(function ($q) {
                            $q->where('status', 'Rated')
                                ->where('satisfaction_rate', '<', 3);
                        });
                    } elseif ($value === 'Passive') {
                        $query->where(function ($q) {
                            $q->where('status', 'Rated')
                                ->where('satisfaction_rate', 3);
                        });
                    } elseif ($value !== '') {
                        $query->where('status', $value);
                    }
                }),


            DateFilter::make('Due From')
                ->config([
                    // 'min' => '2020-01-01',
                    // 'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('updated_at', '>=', $value);
                }),
            DateFilter::make('Due To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('updated_at', '<=', $value);
                })
        ];
    }
}