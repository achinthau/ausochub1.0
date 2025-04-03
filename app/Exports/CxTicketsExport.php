<?php
namespace App\Exports;

use App\Models\CxTicket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CxTicketsExport implements FromCollection, WithHeadings, WithMapping
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

    public function headings(): array
    {
        return [
            'ID', 'Category', 'Product', 'Model', 'Work Order No', 
            'Service Center', 'Warranty Status', 'Sold Date', 
            'Customer Name', 'Customer Address', 'Customer Contact 01', 
            'Customer Contact 02', 'Technician Name', 'Technician Contact', 
            'Supervisor Name', 'Supervisor Contact', 'Ticket Creator', 
            'Status', 'Created At', 'Updated At'
        ];
    }

    /**
     * Map the data to match the headings
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->category,
            $row->product,
            $row->model,
            $row->work_order_no,
            $row->service_center,
            $row->warranty_status,
            $row->sold_date,
            $row->customer_name,
            $row->customer_address,
            $row->customer_contact_01,
            $row->customer_contact_02,
            $row->technician_name,
            $row->technician_contact,
            $row->supervisor_name,
            $row->supervisor_contact,
            $row->creator,
            $row->status,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
