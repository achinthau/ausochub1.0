<?php

namespace App\Http\Livewire\Reports\Partials;

use App\Models\FeedContactValid;
use App\Models\FeedContactValidReport;
use Livewire\Component;

class TodayDialerDataModal extends Component
{
    public $showDataModal = false;

    public $dataRows = [];

    public $rawData = null;

    public $recordId = null;

    protected $listeners = ['showData' => 'showData'];

    public function showData($rowKey)
    {
        if (! auth()->check() || ! auth()->user()->can('is-admin')) {
            return;
        }

        $this->reset(['dataRows', 'rawData', 'recordId']);

        $rowKey = (string) $rowKey;
        $record = null;

        if (str_starts_with($rowKey, 'v-')) {
            $record = FeedContactValid::find((int) substr($rowKey, 2));
        } elseif (str_starts_with($rowKey, 'r-')) {
            $record = FeedContactValidReport::find((int) substr($rowKey, 2));
        }

        if ($record) {
            $this->recordId = $record->id;

            $decoded = json_decode((string) $record->data, true);

            if (is_array($decoded)) {
                $this->dataRows = $this->flattenData($decoded);
            } else {
                $this->rawData = (string) $record->data;
            }
        }

        $this->showDataModal = true;
    }

    protected function flattenData(array $data, string $prefix = ''): array
    {
        $rows = [];

        foreach ($data as $key => $value) {
            $label = $this->formatLabel((string) $key);
            $label = $prefix === '' ? $label : $prefix . ' / ' . $label;

            if (is_array($value)) {
                $rows = array_merge($rows, $this->flattenData($value, $label));

                continue;
            }

            $rows[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $rows;
    }

    protected function formatLabel(string $key): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $key));
    }

    public function render()
    {
        return view('livewire.reports.partials.today-dialer-data-modal');
    }
}
