<?php

namespace App\Http\Livewire\Reports;

use App\Models\CallbackCustomer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use WireUi\View\Components\Select;

class CallbackCustomerTable extends LivewireDatatable
{
    // public $model = CallbackCustomer::class;

    protected $listeners = ['reminderTimeUpdated' => 'refreshTable',];
    public $exportable = true;
    public $hideable = 'select';

    public function refreshTable()
    {
        $this->resetPage();
    }

//     public function builder()
// {
//     return CallbackCustomer::query()
//         ->select('id', 'lead_id', 'agent_id', 'unique_id', 'callback_at', 'called_at', 'comment');
// }
    public function builder()
{
    return CallbackCustomer::query()
    
        ->select('callback_customers.*')->with('users')->whereDate('callback_at', Carbon::today())->orderBy('callback_at', 'desc');
}




    public function columns()
    {

        return [
            // Column::name('id')->label('ID')->filterable()->searchable(),

            Column::name('lead_id')->label('Lead ID')->filterable()->searchable(),

            // Column::name('agent_id')->label('Agent ID')->filterable()->searchable(),

            Column::name('users.name')->label('Agent ID')->filterable()->searchable(),

            Column::name('unique_id')->label('Call Unique ID')->filterable()->searchable(),

            DateColumn::name('callback_at')
                ->label('Callback At')
                ->format('Y-m-d H:i:s')
                ->filterable()
                ->hide(),

            

            Column::name('comment')->label('Comment')->truncate(30)->searchable(),

            DateColumn::name('called_at')
                ->label('Called At')
                ->format('Y-m-d H:i:s')
                ->filterable(),

            Column::callback(['id', 'unique_id'], function ($id, $unique_id) {
                return view('table-actions-v2', [
                    'id' => $id,
                    'uniqueid' => $unique_id
                ]);
            })->label('Call Record')->unsortable()->excludeFromExport(),

            Column::callback(['id'], function ($id) {
                return view('table-actions-v2-copy', [
                    'id' => $id,
                ]);
            })->label('Actions')->unsortable()->excludeFromExport(),
        ];
    }


    public function download($id)
    {
        return Storage::disk('asterisk-media-server')->download("$id.wav");
    }

    public function export(string $filename = 'callback_customer.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('callback_customer_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
