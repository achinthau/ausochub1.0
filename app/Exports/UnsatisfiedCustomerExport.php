<?php

namespace App\Exports;

use App\Models\AppModelsCdr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnsatisfiedCustomerExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

     public $customers;
 
    public function __construct($customers) {
        $this->customers = $customers;
    }


    public function collection(): Collection
    {
        return $this->customers->map(function ($cdr) {
            return [
                'ID' => $cdr->id,
                'Calldate' => $cdr->calldate,
                'Sorce' => $cdr->src,
                'Destination' => $cdr->dst,
                'Dcontext' => $cdr->dcontext,
                'Channel' => $cdr->channel,
                'Duration' => $cdr->duration,
                'Billsec' => $cdr->billsec,
                'Disposition' => $cdr->disposition,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Calldate', 'Sorce', 'Destination', 'Dcontext', 'Channel', 'Duration', 'Billsec', 'Disposition'];
    }
}
