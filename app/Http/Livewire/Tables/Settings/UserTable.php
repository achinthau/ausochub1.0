<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserType;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserTable extends LivewireDatatable
{

    protected $listeners = ['exportManual' => 'exportManual'];
    public $hideable = 'select';
    // public $exportable = true;
    
    // public function builder()
    // {
    //     return User::leftJoin('user_types','user_types.id','users.user_type_id');
    // }

    // public function columns()
    // {
    //     return [
    //         Column::name('id')->searchable(),
    //         Column::name('name')->filterable()->searchable(),
    //         Column::name('email')->filterable()->searchable(),
    //         Column::name('user_name')->filterable()->searchable(),
    //         Column::name('userType.title')->filterable(/* UserType::all()->pluck('title') */)->searchable(),
    //         Column::name('extension')->filterable()->label('Extension'),
    //         Column::name('tenant_context')->filterable()->label('Tenant Contex'),
    //         DateColumn::name('created_at')->filterable()->searchable(),

    //         Column::callback('id', function ($id) {
    //             return view('table-common-actions', ['id' => $id]);
    //         })->unsortable()->excludeFromExport()
    //     ];
    // }


  public function builder()
{
    $contexts = explode(',', auth()->user()->tenant_context);
    $contexts = array_map('trim', $contexts);

    $query = User::query()
        ->leftJoin('user_types', 'user_types.id', 'users.user_type_id')
        ->leftJoin('companies', 'companies.id', '=', 'users.tenant_context')
        ->where(function ($query) use ($contexts) {
            foreach ($contexts as $context) {
                $query->orWhere('users.tenant_context', 'LIKE', '%' . $context . '%');
            }
        })
        ->select(
            'users.*',
            'user_types.title as user_type_title',
            'companies.name as company_name'
        );

    if (!empty($this->activeDateFilters)) {
        foreach ($this->activeDateFilters as $filter) {
            if (!empty($filter['start'])) {
                $query->where('users.created_at', '>=', $filter['start']);
            }
            if (!empty($filter['end'])) {
                $query->where('users.created_at', '<=', $filter['end']);
            }
        }
    }

    return $query;
}






public function columns()
{
    $userType = new UserType();
    $userTypeTable = $userType->getTable();
    $companyTable = 'companies'; 

    return [
        Column::name('id')->searchable(),
        Column::name('name')->filterable()->searchable(),
        Column::name('email')->filterable()->searchable(),
        Column::name('user_name')->filterable()->searchable(),
        // Column::name("$userTypeTable.title")->filterable(UserType::all()->pluck('title')->toArray())->label('User Type')->searchable(),
        Column::name("$userTypeTable.title")->filterable()->label('User Type')->searchable(),
        // Column::name("$companyTable.name")->filterable()->label('Company')->searchable(),

        Column::name('extension')->filterable()->label('Extension'),
        Column::name('tenant_context')->filterable()->label('Tenant Context'),
        DateColumn::name('created_at')->filterable()->searchable(),

        Column::callback('id', function ($id) {
            return view('table-common-actions', ['id' => $id]);
        })->unsortable()->excludeFromExport()
    ];
}

public function doDatetimeFilterStart($index, $start)
{
    if ($start != "") {
        $start = str_replace('T', ' ', $start); // Fix format
        $start = date('Y-m-d H:i:s', strtotime($start)); // Normalize
    }

    $this->activeDateFilters[$index]['start'] = $start;
    $this->page = 1;
    $this->setSessionStoredFilters();
}

public function doDatetimeFilterEnd($index, $end)
{
    if ($end != "") {
        $end = str_replace('T', ' ', $end);
        $end = date('Y-m-d H:i:s', strtotime($end));
    }

    $this->activeDateFilters[$index]['end'] = $end;
    $this->page = 1;
    $this->setSessionStoredFilters();
}


public function exportManual()
    {
        $data = $this->builder()->get(); // Get filtered data

        return Excel::download(new class($data) implements FromCollection, WithHeadings, WithMapping {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Name',
                    'Email',
                    'Username',
                    'User Type',
                    'Extension',
                    'Tenant Context',
                    'Created At',
                ];
            }

            public function map($row): array
            {
                return [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->user_name,
                    $row->user_type_title,
                    $row->extension,
                    $row->tenant_context,
                    $row->created_at,
                ];
            }
        }, 'users_export_' . now()->format('Ymd_His') . '.xlsx');
    }



    
}