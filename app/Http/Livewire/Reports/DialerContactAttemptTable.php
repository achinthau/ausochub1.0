<?php

namespace App\Http\Livewire\Reports;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\DialerCallStatusOption;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\FeedContactValid;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;



class DialerContactAttemptTable extends DataTableComponent
{
    protected $model = FeedContactValid::class;

    public $selectedCampaign = null;
    protected $listeners = ['dialerCampUpdated' => 'setCampaign', 'refreshTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function setCampaign($cpmId)
    {
        $this->selectedCampaign = $cpmId;
        // $this->resetPage();
        $this->emitSelf('refreshTable');
    }
    public function builder(): Builder
    {
        if ($this->selectedCampaign) {
            return FeedContactValid::query()
                ->with(['campaign', 'updater'])->orderByDesc('attempted_at')
                ->where('campaign_id', $this->selectedCampaign);
        } else {
            $user = auth()->user();
            $userTenants = array_map('trim', explode(',', $user->tenant_context));
            $tenantIds = Company::whereIn('name', $userTenants)->pluck('id');
            $campaignIds = Campaign::whereIn('company', $tenantIds)->pluck('id');
            return FeedContactValid::query()
                ->with(['campaign', 'updater'])->orderByDesc('attempted_at')
                ->whereIn('campaign_id', $campaignIds);
        }
    }




    protected function statusLabel($status): string
    {
        $status = (string) $status;

        return [
            '1' => 'Answered',
            '41' => 'Canceled Answered',
            '2' => 'Not Answered',
            '42' => 'Canceled Not Answered',
            '22' => 'Not Answered',
            '222' => 'Not Answered',
            '3' => 'Skipped',
            '4' => 'Canceled',
            '5' => 'Change Request',
        ][$status] ?? 'N/A';
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->searchable(),
            Column::make("Tracking_code", "priority_field")
                ->format(function ($value, $row) {
                    return $row->priority_field ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('priority_field', 'like', '%' . $term . '%');
                }),
            Column::make("Contact_no_01", "contact_no_01")
                ->format(function ($value, $row) {
                    return $row->contact_no_01 ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('contact_no_01', 'like', '%' . $term . '%');
                }),
            Column::make("Contact_no_02", "contact_no_02")
                ->format(function ($value, $row) {
                    return $row->contact_no_02
                        ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('contact_no_02', 'like', '%' . $term . '%');
                }),
            Column::make("Call status option", "call_status_option_id")
                ->format(function ($value, $row) {
                    return $row->call_status_option_id ?: 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('call_status_option_id', 'like', '%' . $term . '%');
                }),
            Column::make("Call status", "status")
                ->format(function ($value, $row) {
                    return $this->statusLabel($row->status) ?: 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    $statusMap = [
                        '1' => 'Answered',
                        '41' => 'Canceled Answered',
                        '2' => 'Not Answered',
                        '42' => 'Canceled Not Answered',
                        '22' => 'Not Answered',
                        '222' => 'Not Answered',
                        '3' => 'Skipped',
                        '4' => 'Canceled',
                        '5' => 'Change Request',
                    ];

                    $matchedStatuses = collect($statusMap)
                        ->filter(fn ($label) => stripos($label, $term) !== false)
                        ->keys();

                    if ($matchedStatuses->isEmpty()) {
                        return $builder;
                    }

                    return $builder->orWhere(function ($query) use ($matchedStatuses) {
                        foreach ($matchedStatuses as $status) {
                            $query->orWhere('status', $status);
                        }
                    });
                }),

            Column::make("Comments", "comments")
                ->sortable()
                ->searchable(),
            Column::make("Campaign", "campaign_id")
                ->format(fn($value, $row) => optional($row->campaign)->name ?? '—')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('campaign', function ($query) use ($term) {
                        $query->where('name', 'like', '%' . $term . '%');
                    });
                }),

            Column::make("Updated by", "updated_by")
                ->format(function ($value, $row) {
                    $user = $row->updater;
                    return $user->name ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('updater', function ($query) use ($term) {
                        $query->where('name', 'like', '%' . $term . '%');
                    });
                }),
            // Column::make("Created at", "created_at")
            //     ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [

            SelectFilter::make('Call Status')
    ->options([
        '' => 'All',      // default option
        '1' => 'Answered',
        '2' => 'Not Answered',
        '3' => 'Skipped',
        '4' => 'Canceled',
        '5' => 'Change Request',
    ])
    ->filter(function ($builder, $value) {
        if ($value !== '') {
            $statuses = match ($value) {
                '1' => [1, 41],
                '2' => [2, 22, 222, 42],
                '3' => [3],
                '4' => [4, 41, 42],
                '5' => [5],
                default => [(int) $value],
            };
            $builder->whereIn('status', $statuses);
        }
    }),

            
            // ✅ Call Status Option (Dynamic)
            SelectFilter::make('Call Status Option')
                ->options(
                    DialerCallStatusOption::pluck('option', 'option')
                        ->prepend('All', '')
                        ->toArray() // ✅ Convert to array
                )
                ->filter(function ($builder, $value) {
                    if ($value !== '') {
                        $builder->whereRaw("FIND_IN_SET(?, REPLACE(call_status_option_id, ' ', ''))", [$value]);
                    }
                }),

            // ✅ Updated By (Dynamic Users)
            SelectFilter::make('Updated By')
                ->options(
                    User::pluck('name', 'id')
                        ->prepend('All', '')
                        ->toArray() // ✅ Convert to array
                )
                ->filter(function ($builder, $value) {
                    if ($value !== '') {
                        $builder->where('updated_by', $value);
                    }
                }),

            SelectFilter::make('Campaign')
                ->options(
                    Campaign::pluck('name', 'id')
                        ->prepend('All', '')
                        ->toArray()
                )
                ->filter(function ($builder, $campaignId) {
                    if ($campaignId !== '') {
                        $builder->where('campaign_id', $campaignId);
                    }
                }),


        ];
    }


    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];


    public function exportSelected()
    {
        // Get the selected IDs
        $selectedIds = $this->getSelected();

        if (empty($selectedIds)) {
            return; // Or add a notification like: $this->notify('No rows selected for export.');
        }

        // Fetch the selected records with relationships
        $records = FeedContactValid::with(['updater', 'campaign'])
            ->whereIn('id', $selectedIds)
            ->get();

        // Define CSV headers matching the table columns
        $headers = [
            'ID',
            'Priority Field',
            'Contact No 01',
            'Contact No 02',
            'Call Status Option',
            'Comments',
            'Campaign',
            'Updated By',
            'Updated At',
        ];

        // Prepare CSV content
        $csvData = [];
        $csvData[] = $headers; // Add headers as the first row

        // Format each record to match the table's column display logic
        foreach ($records as $record) {
            $csvData[] = [
                $record->id,
                $record->priority_field ?? 'N/A',
                $record->contact_no_01 ?? 'N/A',
                $record->contact_no_02 ?? 'N/A',
                                $record->call_status_option_id ?: 'N/A',
                $record->comments ?? 'N/A',
                optional($record->campaign)->name ?? 'N/A',
                optional($record->updater)->name ?? 'N/A',
                $record->attempted_at ? $record->attempted_at->toDateTimeString() : 'N/A',            ];
        }

        // Generate CSV content
        $csvContent = '';
        foreach ($csvData as $row) {
            // Escape values to handle commas, quotes, etc.
            $row = array_map(function ($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row);
            $csvContent .= implode(',', $row) . "\n";
        }

        // Store the CSV file in the storage disk
        $fileName = 'dialer_call_history_' . Carbon::now()->format('Ymd_His') . '.csv';
        Storage::disk('local')->put('exports/' . $fileName, $csvContent);

        // Clear selected rows
        $this->clearSelected();

        // Return the file for download
        return response()->download(storage_path('app/exports/' . $fileName))->deleteFileAfterSend(true);
    }


}
