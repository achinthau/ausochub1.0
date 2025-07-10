<?php

namespace App\Exports;

use App\Models\AgentLoginLogoutDetail;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgentLoginLogoutExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Id',
            'Agent ID',
            'Agent Name',
            'Login Time',
            'Logout Time',
        ];
    }

}
