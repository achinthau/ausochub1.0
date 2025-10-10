<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TicketExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Ticket::whereIn('id', $this->tickets)->get();
    }

    public $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->topic,
            $ticket->description,
            $ticket->lead?->contact_number,
            $ticket->category?->title,
            $ticket->subCategory?->title,
            $ticket->tags ? implode(', ', $ticket->tags) : null,
            $ticket->outlet?->title,
            $ticket->department?->name,
            $ticket->assignedUser?->name,
            $ticket->due_at,
            $ticket->created_at,
            $ticket->updated_at,
        ];
    }

    // Column headers
    public function headings(): array
    {
        return [
            'ID',
            'Topic',
            'Description',
            'Contact',
            'Category',
            'Sub Category',
            'Tags',
            'Outlet',
            'Department',
            'Assigned To',
            'Due At',
            'Created At',
            'Updated At',
        ];
    }
}
