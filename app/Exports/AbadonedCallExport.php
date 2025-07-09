<?php

namespace App\Exports;

use App\Models\AbandonedNew;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbadonedCallExport implements FromArray, WithHeadings
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
            'From',
            'To',
            'Recalled Status',
            'Received',
            'Recalled',
        ];
    }


    
}
