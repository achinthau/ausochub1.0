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
use App\Models\FeedContactAttempt;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;



class DialerContactAttemptTable extends DataTableComponent
{
    protected $model = FeedContactAttempt::class;

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
            return FeedContactAttempt::query()
                ->with(['feed', 'status', 'agent', 'campaign'])->orderByDesc('id')
                ->where('campaign_id', $this->selectedCampaign);
        } else {
            $user = auth()->user();
            $userTenants = array_map('trim', explode(',', $user->tenant_context));
            $tenantIds = Company::whereIn('name', $userTenants)->pluck('id');
            $campaignIds = Campaign::whereIn('company', $tenantIds)->pluck('id');
            return FeedContactAttempt::query()
                ->with(['feed', 'status', 'agent', 'campaign'])->orderByDesc('id')
                ->whereIn('campaign_id', $campaignIds);
        }
    }




    protected $statusOptionsCache = null;

    protected function statusOptionsMap(): array
    {
        if ($this->statusOptionsCache === null) {
            $this->statusOptionsCache = DialerCallStatusOption::query()
                ->get(['id', 'option', 'type'])
                ->mapWithKeys(fn ($option) => [$option->id => ['option' => $option->option, 'type' => $option->type]])
                ->all();
        }

        return $this->statusOptionsCache;
    }

    protected function optionIds($value): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', (string) $value)),
            fn ($id) => $id !== ''
        ));
    }

    protected function optionNames($value): string
    {
        $map = $this->statusOptionsMap();
        $names = [];

        foreach ($this->optionIds($value) as $id) {
            if (isset($map[$id])) {
                $names[] = $map[$id]['option'];
            }
        }

        return implode(', ', $names);
    }

    protected function optionTypes($value): string
    {
        $map = $this->statusOptionsMap();
        $types = [];

        foreach ($this->optionIds($value) as $id) {
            if (isset($map[$id])) {
                $types[] = $this->statusTypeLabel($map[$id]['type']);
            }
        }

        return implode(', ', array_values(array_unique($types)));
    }

    protected function statusTypeLabel($type): string
    {
        return [
            '1' => 'Answered',
            '2' => 'Not Answered',
            '3' => 'Skipped',
            '4' => 'Not Answered',
        ][(string) $type] ?? 'N/A';
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->searchable(),
            Column::make("Tracking_code", "feed_contact_valid_id")
                ->format(function ($value, $row) {
                    return optional($row->feed)->priority_field ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('feed', function ($query) use ($term) {
                        $query->where('priority_field', 'like', '%' . $term . '%');
                    });
                }),
            Column::make("Contact_no_01", "feed_contact_valid_id")
                ->format(function ($value, $row) {
                    return optional($row->feed)->contact_no_01 ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('feed', function ($query) use ($term) {
                        $query->where('contact_no_01', 'like', '%' . $term . '%');
                    });
                }),
            Column::make("Contact_no_02", "feed_contact_valid_id")
                ->format(function ($value, $row) {
                    $contact = $row->feed;
                    return $contact->contact_no_02
                        ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('feed', function ($query) use ($term) {
                        $query->where('contact_no_02', 'like', '%' . $term . '%');
                    });
                }),
            Column::make("Call status option", "call_status_option_id")
                ->format(function ($value, $row) {
                    return $this->optionNames($row->call_status_option_id) ?: 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    $optionIds = DialerCallStatusOption::where('option', 'like', '%' . $term . '%')
                        ->pluck('id')
                        ->map(fn ($id) => (string) $id);

                    if ($optionIds->isEmpty()) {
                        return $builder;
                    }

                    return $builder->orWhere(function ($query) use ($optionIds) {
                        foreach ($optionIds as $id) {
                            $query->orWhereRaw("FIND_IN_SET(?, REPLACE(call_status_option_id, ' ', ''))", [$id]);
                        }
                    });
                }),
            Column::make("Call status", "call_status_option_id")
                ->format(function ($value, $row) {
                    return $this->optionTypes($row->call_status_option_id) ?: 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    $typeMap = [
                        1 => 'Answered',
                        2 => 'Not Answered',
                        3 => 'Skipped',
                    ];

                    $matchedTypes = collect($typeMap)
                        ->filter(fn ($label) => stripos($label, $term) !== false)
                        ->keys();

                    if ($matchedTypes->isEmpty()) {
                        return $builder;
                    }

                    $typeIds = DialerCallStatusOption::whereIn('type', $matchedTypes)
                        ->pluck('id')
                        ->map(fn ($id) => (string) $id);

                    if ($typeIds->isEmpty()) {
                        return $builder;
                    }

                    return $builder->orWhere(function ($query) use ($typeIds) {
                        foreach ($typeIds as $id) {
                            $query->orWhereRaw("FIND_IN_SET(?, REPLACE(call_status_option_id, ' ', ''))", [$id]);
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
                    $user = $row->agent;
                    return $user->name ?? 'N/A';
                })
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('agent', function ($query) use ($term) {
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

            SelectFilter::make('Call Status Type')
    ->options([
        '' => 'All',      // default option
        1  => 'Answered',
        2  => 'NotAnswered',
        3  => 'Skipped',
    ])
    ->filter(function ($builder, $value) {
        if ($value !== '') {
            $typeIds = DialerCallStatusOption::where('type', $value)
                ->pluck('id')
                ->map(fn ($id) => (string) $id);

            if ($typeIds->isEmpty()) {
                $builder->whereRaw('1 = 0');
            } else {
                $builder->where(function ($query) use ($typeIds) {
                    foreach ($typeIds as $id) {
                        $query->orWhereRaw("FIND_IN_SET(?, REPLACE(call_status_option_id, ' ', ''))", [$id]);
                    }
                });
            }
        }
    }),

            
            // ✅ Call Status Option (Dynamic)
            SelectFilter::make('Call Status Option')
                ->options(
                    DialerCallStatusOption::pluck('option', 'id')
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
                        $assignedFeeds = Campaign::where('id', $campaignId)
                            ->value('assigned_feeds'); // e.g., "101,100"
        
                        if ($assignedFeeds) {
                            $feedIds = array_map('trim', explode(',', $assignedFeeds));

                            // Filter attempts whose feed_contact_valid.feed_id is in assigned feeds
                            $builder->whereHas('feed', function ($query) use ($feedIds) {
                                $query->whereIn('feed_id', $feedIds);
                            });
                        }
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
        $records = FeedContactAttempt::with(['feed', 'agent'])
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
                optional($record->feed)->priority_field ?? 'N/A',
                optional($record->feed)->contact_no_01 ?? 'N/A',
                optional($record->feed)->contact_no_02 ?? 'N/A',
                $this->optionNames($record->call_status_option_id) ?: 'N/A',
                $record->comments ?? 'N/A',
                optional($record->campaign)->name ?? 'N/A',
                optional($record->agent)->name ?? 'N/A',
                $record->updated_at ? $record->updated_at->toDateTimeString() : 'N/A',
            ];
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
