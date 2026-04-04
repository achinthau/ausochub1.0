<?php
namespace App\Exports;

use App\Models\AgentPerformance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgentPerformanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        // Ensure we have a collection
        if (!is_null($data) && !is_array($data) && !$data instanceof \Illuminate\Support\Collection) {
            $this->data = collect([]);
        } else {
            $this->data = is_array($data) ? collect($data) : $data;
        }
    }

    /**
     * Provide the collection of data for export.
     */
    public function collection()
    {
        return $this->data ?? collect([]);
    }

    public function headings(): array
    {
        return [
            'Date', 'Agent Name', 'Extension', 'Total Calls', 
            'Avg Calls', 'Total Missed', 'Avg Missed', 'ACW (m)', 
            'Avg ACW (m)', 'Break (m)', 'Avg Break (m)', 'Active (m)', 
            'Talk (m)', 'Avg Talk (m)', 'Tickets', 'Queues'
        ];
    }

    /**
     * Map the data to match the headings
     */
    public function map($row): array
    {
        // Handle both object and array access
        $getId = function($obj, $key, $default = null) {
            if (is_array($obj)) {
                return $obj[$key] ?? $default;
            }
            return $obj->$key ?? $default;
        };

        $date = $getId($row, 'date');
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d');
        }

        $queues = $getId($row, 'queues', []);
        if (is_string($queues)) {
            $queues = json_decode($queues, true);
        }
        if (is_array($queues) && !empty($queues)) {
            $queueStr = json_encode($queues);
        } else {
            $queueStr = '';
        }

        return [
            $date,
            $getId($row, 'agent_name'),
            $getId($row, 'extension'),
            $getId($row, 'total_calls'),
            number_format($getId($row, 'avg_calls', 0), 2),
            $getId($row, 'total_missed'),
            number_format($getId($row, 'avg_missed', 0), 2),
            number_format(($getId($row, 'acw', 0) / 60), 2),
            number_format(($getId($row, 'avg_acw', 0) / 60), 2),
            number_format(($getId($row, 'other_break', 0) / 60), 2),
            number_format(($getId($row, 'avg_oth_break', 0) / 60), 2),
            number_format(($getId($row, 'active_time', 0) / 60), 2),
            number_format(($getId($row, 'talk_time', 0) / 60), 2),
            number_format(($getId($row, 'avg_talk_time', 0) / 60), 2),
            $getId($row, 'tickets_created_count'),
            $queueStr,
        ];
    }
}

