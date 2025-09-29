<?php

namespace App\Http\Livewire\Reports;

use App\Models\DialerCallStatusOption;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\FeedContactAttempt;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class DialerContactAttemptTable extends DataTableComponent
{
    protected $model = FeedContactAttempt::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Priority Field", "feed_contact_valid_id")
                ->format(function ($value, $row) {
                    return optional($row->feed)->priority_field ?? 'N/A';
                })
                ->sortable(),
            Column::make("Contact_no_01", "feed_contact_valid_id")
                ->format(function ($value, $row) {
                    $contact = $row->feed;
                    return $contact->contact_no_01
                        ?? 'N/A';
                })
                ->sortable(),
            Column::make("Contact_no_02", "feed_contact_valid_id")
                ->format(function ($value, $row) {
                    $contact = $row->feed;
                    return $contact->contact_no_02
                        ?? 'N/A';
                })
                ->sortable(),
            Column::make("Call status option", "call_status_option_id")
                ->format(function ($value, $row) {
                    $status = $row->status;
                    return $status->option ?? 'N/A';
                })
                ->sortable(),
            Column::make("Comments", "comments")
                ->sortable(),
            Column::make("Updated by", "updated_by")
                ->format(function ($value, $row) {
                    $user = $row->agent;
                    return $user->name ?? 'N/A';
                })
                ->sortable(),
            // Column::make("Created at", "created_at")
            //     ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            // ✅ Call Status Option (Dynamic)
            SelectFilter::make('Call Status Option')
                ->options(
                    DialerCallStatusOption::pluck('option', 'id')
                        ->prepend('All', '')
                        ->toArray() // ✅ Convert to array
                )
                ->filter(function ($builder, $value) {
                    if ($value !== '') {
                        $builder->where('call_status_option_id', $value);
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
    $records = FeedContactAttempt::with(['feed', 'status', 'agent'])
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
            optional($record->status)->option ?? 'N/A',
            $record->comments ?? 'N/A',
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
    $fileName = 'dialer_contact_attempts_' . Carbon::now()->format('Ymd_His') . '.csv';
    Storage::disk('local')->put('exports/' . $fileName, $csvContent);

    // Clear selected rows
    $this->clearSelected();

    // Return the file for download
    return response()->download(storage_path('app/exports/' . $fileName))->deleteFileAfterSend(true);
}


}
