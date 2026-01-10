<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyQueueSummeryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Provide the collection of data for export.
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Define the column headings for the export file.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Queue',
            'Calls',
            'Answered',
            'Abandoned',
            'Agents',
        ];
    }

    /**
     * Map each row of data to the required format.
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->date,
            $row->queue,
            $row->calls,
            $row->answered,
            $row->abandoned,
            $row->agents,
        ];
    }
}
