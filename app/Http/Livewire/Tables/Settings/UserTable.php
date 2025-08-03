<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserType;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class UserTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;
    
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
    $user = new User();
    $userType = new UserType();

    $userTable = $user->getTable();       
    $userTypeTable = $userType->getTable(); 
    $companyTable = 'companies';          

    return User::leftJoin($userTypeTable, "$userTypeTable.id", "$userTable.user_type_id")
               ->leftJoin($companyTable, "$companyTable.id", "$userTable.tenant_context")
               ->select("$userTable.*", "$userTypeTable.title as user_type_title", "$companyTable.name as company_name");
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
        Column::name("$companyTable.name")->filterable()->label('Company')->searchable(),
        Column::name('extension')->filterable()->label('Extension'),
        Column::name('tenant_context')->filterable()->label('Tenant Context'),
        DateColumn::name('created_at')->filterable()->searchable(),

        Column::callback('id', function ($id) {
            return view('table-common-actions', ['id' => $id]);
        })->unsortable()->excludeFromExport()
    ];
}


    
}