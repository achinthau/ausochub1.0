<?php

namespace App\Http\Livewire\Reports;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\FeedContactValid;
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
    protected $model = FeedContactValid::class;

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
        $query = FeedContactValid::query()
            ->with(['updater', 'campaign', 'campaign.types'])
            ->whereDate('attempted_at', Carbon::today());

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

    protected function statusLabel($status): string
    {
        $status = (string) $status;

        return [
            '1' => 'Answered',
            '41' => 'Answered Canceled',
            '2' => 'Not Answered',
            '42' => 'Not-Answered Canceled',
            '22' => 'Not-Answered',
            '222' => 'Not-Answered',
            '3' => 'Skipped',
            '4' => 'Canceled',
            '5' => 'Change Request',
            '51' => 'Change Request Answered',
            '52' => 'Change Request Not Answered',
        ][$status] ?? 'N/A';
    }

    public function columns(): array
    {
        $columns = [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Priority Field", "priority_field")
                ->format(fn ($value, $row) => $row->priority_field ?? 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('priority_field', 'like', '%' . $term . '%');
                }),

            // Column::make("Contact No 01", "contact_no_01")
            //     ->format(fn ($value, $row) => $row->contact_no_01 ?? 'N/A')
            //     ->sortable()
            //     ->searchable(function ($builder, $term) {
            //         return $builder->orWhere('contact_no_01', 'like', '%' . $term . '%');
            //     }),

            // Column::make("Contact No 02", "contact_no_02")
            //     ->format(fn ($value, $row) => $row->contact_no_02 ?? 'N/A')
            //     ->sortable()
            //     ->searchable(function ($builder, $term) {
            //         return $builder->orWhere('contact_no_02', 'like', '%' . $term . '%');
            //     }),

            // Column::make("Language", "lang")
            //     ->format(fn ($value, $row) => $row->lang ?: 'N/A'),
        ];

        if ($this->isAdmin()) {
            $columns[] = Column::make("Agent", "updated_by")
                ->format(fn ($value, $row) => optional($row->updater)->name ?? 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhereHas('updater', function ($query) use ($term) {
                        $query->where('name', 'like', '%' . $term . '%');
                    });
                });
        }

        return array_merge($columns, [
            // Column::make("Campaign Type", "campaign_id")
            //     ->format(fn ($value, $row) => optional($row->campaign?->types)->name ?? 'N/A'),

            Column::make("Call Status", "status")
                ->format(fn ($value, $row) => $this->statusLabel($row->status) ?: 'N/A')
                ->sortable(),
                
            Column::make("Call Status Option", "call_status_option_id")
                ->format(fn ($value, $row) => $row->call_status_option_id ?: 'N/A')
                ->sortable()
                ->searchable(function ($builder, $term) {
                    return $builder->orWhere('call_status_option_id', 'like', '%' . $term . '%');
                }),

            

            Column::make("Rate", "rate")
                ->sortable(),

            Column::make("Comments", "comments")
                ->sortable()
                ->searchable(),

            // Column::make("Campaign", "campaign_id")
            //     ->format(fn ($value, $row) => optional($row->campaign)->name ?? '—')
            //     ->sortable()
            //     ->searchable(function ($builder, $term) {
            //         return $builder->orWhereHas('campaign', function ($query) use ($term) {
            //             $query->where('name', 'like', '%' . $term . '%');
            //         });
            //     }),

            Column::make("Called At", "attempted_at")
                ->sortable(),
        ]);
    }

    public function filters(): array
    {
        $filters = [
            SelectFilter::make('Call Status')
                ->options([
                    '' => 'All',
                    '1' => 'Answered',
                    '2' => 'Not Answered',
                    // '3' => 'Skipped',
                    '4' => 'Canceled',
                    '41' => 'Answered Canceled',
                    '42' => 'Not-Answered Canceled',
                    '5' => 'Change Request',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $statuses = match ($value) {
                            '1' => [1, 41, 51],
                            '2' => [2, 22, 222, 42, 52],
                            '3' => [3],
                            '4' => [4, 41, 42],
                            '5' => [5, 51, 52],
                            default => [(int) $value],
                        };
                        $builder->whereIn('status', $statuses);
                    }
                }),
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

        $records = FeedContactValid::with(['updater', 'campaign'])
            ->whereIn('id', $selectedIds)
            ->get();

        $headers = [
            'ID',
            'Priority Field',
            // 'Contact No 01',
            // 'Contact No 02',
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
                $record->priority_field ?? 'N/A',
                // $record->contact_no_01 ?? 'N/A',
                // $record->contact_no_02 ?? 'N/A',
                $record->call_status_option_id ?: 'N/A',
                $this->statusLabel($record->status) ?: 'N/A',
                $record->rate ?? 'N/A',
                $record->comments ?? 'N/A',
                optional($record->campaign)->name ?? 'N/A',
                optional($record->updater)->name ?? 'N/A',
                $record->attempted_at ? $record->attempted_at->toDateTimeString() : 'N/A',
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
