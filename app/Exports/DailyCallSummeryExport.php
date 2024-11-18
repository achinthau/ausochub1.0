<?php

namespace App\Exports;

use App\Models\DailyCallSummary;
use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DailyCallSummeryExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DailyCallSummary::whereIn('id', $this->dailyCallSummery)->get();
    }

    public $dailyCallSummery;

    public function __construct($dailyCallSummery)
    {
        $this->dailyCallSummery = $dailyCallSummery;
    }
}
