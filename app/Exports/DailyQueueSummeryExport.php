<?php

namespace App\Exports;

use App\Models\DailyQueueSummery;
use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DailyQueueSummeryExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DailyQueueSummery::whereIn('id', $this->dailyQueueSummery)->get();
    }

    public $dailyQueueSummery;

    public function __construct($dailyQueueSummery)
    {
        $this->dailyQueueSummery = $dailyQueueSummery;
    }
}
