<?php

namespace App\Http\Livewire\Reports;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class TodayDialerReportTable extends DataTableComponent
{
    protected $model = FeedContactAttempt::class;

    public $selectedCampaign = null;

    protected $listeners = [
        'todayDialerCampaignSelected' => 'setCampaign',
        'refreshTable' => '$refresh',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            ->setSearchEnabled()
            ->setColumnSelectEnabled()
            ->setFiltersEnabled()
            ->setPerPage(25);
    }

    public function setCampaign($campaignId)
    {
        $this->selectedCampaign = $campaignId ? (int) $campaignId : null;
        $this->emitSelf('refreshTable');
    }

    protected function isAdmin(): bool
    {
        return auth()->user()->can('is-admin');
    }

    protected function accessibleCampaignIds(): array
    {
        $user = auth()->user();

        $query = Campaign::query();

        if ($this->isAdmin()) {
            $userContexts = array_map('trim', explode(',', $user->tenant_context));
            $contextIds = Company::whereIn('name', $userContexts)->pluck('id');
            $query->whereIn('company', $contextIds);
        } else {
            $query->whereRaw('FIND_IN_SET(?, assigned_users)', [$user->id]);
        }

        return $query->pluck('id')->toArray();
    }

    public function builder(): Builder
    {
        $query = FeedContactAttempt::query()
            ->with(['feed', 'agent', 'campaign', 'campaign.types'])
            ->whereDate('created_at', Carbon::today());

        if ($this->selectedCampaign) {
            $query->where('campaign_id', $this->selectedCampaign);
        } else {
            $campaignIds = $this->accessibleCampaignIds();
            if (empty($campaignIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('campaign_id', $campaignIds);
            }
        }

        if (! $this->isAdmin()) {
            $query->where('updated_by', auth()->id());
        }

        return $query;
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

    public function columns(): array
    {
        $columns = [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Priority Field", "feed_contact_valid_id")
                ->format(fn ($value, $row) => optional($row->feed)->priority_field ?? 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('feed', function ($query) use ($term) {
                        $query->where('priority_field', 'like', '%' . $term . '%');
                    });
                }),

            Column::make("Contact No 01", "feed_contact_valid_id")
                ->format(fn ($value, $row) => optional($row->feed)->contact_no_01 ?? 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('feed', function ($query) use ($term) {
                        $query->where('contact_no_01', 'like', '%' . $term . '%');
                    });
                }),

            Column::make("Contact No 02", "feed_contact_valid_id")
                ->format(fn ($value, $row) => optional($row->feed)->contact_no_02 ?? 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('feed', function ($query) use ($term) {
                        $query->where('contact_no_02', 'like', '%' . $term . '%');
                    });
                }),

            Column::make("Language", "feed_contact_valid_id")
                ->format(fn ($value, $row) => optional($row->feed)->lang ?: 'N/A'),
        ];

        return array_merge($columns, [
            // Column::make("Campaign Type", "campaign_id")
            //     ->format(fn ($value, $row) => optional($row->campaign?->types)->name ?? 'N/A'),

            Column::make("Call Status Option", "call_status_option_id")
                ->format(fn ($value, $row) => $this->optionNames($row->call_status_option_id) ?: 'N/A')
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

            Column::make("Call Status", "call_status_option_id")
                ->format(fn ($value, $row) => $this->optionTypes($row->call_status_option_id) ?: 'N/A'),

            Column::make("Rate", "rate")
                ->sortable(),

            Column::make("Comments", "comments")
                ->sortable()
                ->searchable(),

            Column::make("Campaign", "campaign_id")
                ->format(fn ($value, $row) => optional($row->campaign)->name ?? '—')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('campaign', function ($query) use ($term) {
                        $query->where('name', 'like', '%' . $term . '%');
                    });
                }),

            Column::make("Agent", "updated_by")
                ->format(fn ($value, $row) => optional($row->agent)->name ?? 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('agent', function ($query) use ($term) {
                        $query->where('name', 'like', '%' . $term . '%');
                    });
                }),

            Column::make("Called At", "created_at")
                ->sortable(),
        ]);
    }

    public function filters(): array
    {
        $filters = [
            SelectFilter::make('Call Status Type')
                ->options([
                    '' => 'All',
                    '1' => 'Answered',
                    '2' => 'Not Answered',
                    '3' => 'Skipped',
                ])
                ->filter(function (Builder $builder, string $value) {
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

            // SelectFilter::make('Call Status Option')
            //     ->options(
            //         DialerCallStatusOption::query()
            //             ->where(function ($query) {
            //                 $query->whereNull('campaign_id');

            //                 if ($this->selectedCampaign) {
            //                     $query->orWhere('campaign_id', $this->selectedCampaign);
            //                 }
            //             })
            //             ->orderBy('option')
            //             ->pluck('option', 'id')
            //             ->prepend('All', '')
            //             ->toArray()
            //     )
            //     ->filter(function (Builder $builder, string $value) {
            //         if ($value !== '') {
            //             $builder->whereRaw("FIND_IN_SET(?, REPLACE(call_status_option_id, ' ', ''))", [$value]);
            //         }
            //     }),

            // TextFilter::make('Comments')
            //     ->filter(function (Builder $builder, string $value) {
            //         $builder->where('comments', 'like', '%' . $value . '%');
            //     }),
        ];

        if ($this->isAdmin()) {
            $agentIds = Campaign::whereIn('id', $this->accessibleCampaignIds())
                ->pluck('assigned_users')
                ->flatMap(fn ($value) => $value ? array_filter(explode(',', $value)) : [])
                ->unique()
                ->values();

            array_splice($filters, 1, 0, [
                SelectFilter::make('Agent')
                    ->options(
                        User::whereIn('id', $agentIds)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->prepend('All', '')
                            ->toArray()
                    )
                    ->filter(function (Builder $builder, string $value) {
                        if ($value !== '') {
                            $builder->where('updated_by', $value);
                        }
                    }),
            ]);
        }

        return $filters;
    }

    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];

    public function exportSelected()
    {
        $selectedIds = $this->getSelected();

        if (empty($selectedIds)) {
            return;
        }

        $records = FeedContactAttempt::with(['feed', 'agent', 'campaign'])
            ->whereIn('id', $selectedIds)
            ->get();

        $headers = [
            'ID',
            'Tracking Code',
            'Contact No 01',
            'Contact No 02',
            'Call Status Option',
            'Call Status',
            'Rate',
            'Comments',
            'Campaign',
            'Agent',
            'Called At',
        ];

        $csvData = [$headers];

        foreach ($records as $record) {
            $row = [
                $record->id,
                optional($record->feed)->priority_field ?? 'N/A',
                optional($record->feed)->contact_no_01 ?? 'N/A',
                optional($record->feed)->contact_no_02 ?? 'N/A',
                $this->optionNames($record->call_status_option_id) ?: 'N/A',
                $this->optionTypes($record->call_status_option_id) ?: 'N/A',
                $record->rate ?? 'N/A',
                $record->comments ?? 'N/A',
                optional($record->campaign)->name ?? 'N/A',
                optional($record->agent)->name ?? 'N/A',
                $record->created_at ? $record->created_at->toDateTimeString() : 'N/A',
            ];

            $csvData[] = $row;
        }

        $csvContent = '';

        foreach ($csvData as $row) {
            $row = array_map(function ($value) {
                return '"' . str_replace('"', '""', (string) $value) . '"';
            }, $row);
            $csvContent .= implode(',', $row) . "\n";
        }

        $fileName = 'today_dialer_' . Carbon::now()->format('Ymd_His') . '.csv';
        Storage::disk('local')->put('exports/' . $fileName, $csvContent);

        $this->clearSelected();

        return response()->download(storage_path('app/exports/' . $fileName))->deleteFileAfterSend(true);
    }
}
