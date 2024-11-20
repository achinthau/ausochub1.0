<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyCallSummeryExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Provide data as an array.
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Return the column headers.
     */
    public function headings(): array
    {
        return [
            'Id',
            'Date',
            'Inbound',
            'Outbound',
            'Queued',
            'Abandoned',
            'Answered',
        ];
    }
}
