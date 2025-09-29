<?php

namespace App\Http\Livewire\Reports;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\FeedContactAttempt;

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
            Column::make("Call status option","call_status_option_id")
            ->format(function ($value, $row){
                $status = $row->status;
                return $status->option ?? 'N/A';
            })
                ->sortable(),
            Column::make("Comments", "comments")
                ->sortable(),
            Column::make("Updated by","updated_by")
            ->format(function ($value, $row){
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
}
