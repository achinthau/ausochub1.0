<?php

namespace App\Http\Livewire;

use App\Exports\AgentLoginLogoutExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\AgentLoginLogoutDetail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class AgentLoginLogoutTable extends DataTableComponent
{
    protected $model = AgentLoginLogoutDetail::class;

    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setBulkActions([
            'exportSelected' => 'Export',
        ]);
    }

    public function query()
    {
        return AgentLoginLogoutDetail::with('user');
    }

    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }

    public function export()
{
    $selectedIds = $this->getSelected();

    $agentLoginLogout = AgentLoginLogoutDetail::whereIn('id', $selectedIds)->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'Agent ID' => $item->user_id,
            'Agent name' => $item->user->name,
            'Login_time' => $item->login_time,
            'Logout_time' => $item->logout_time,
        ];
    })->toArray();

    $this->clearSelected();

    return Excel::download(new AgentLoginLogoutExport($agentLoginLogout), 'agentLoginLogout.csv');
}

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("User Name", "user.name")
                ->sortable()->searchable(),
            Column::make("Login time", "login_time")
                ->sortable(),
            Column::make("Logout time", "logout_time")
                ->sortable(),
            // Column::make("Created at", "created_at")
            //     ->sortable(),
            // Column::make("Updated at", "updated_at")
            //     ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            
            DateFilter::make('From')
                ->config([
                    // 'min' => '2020-01-01',
                    // 'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('login_time', '>=', $value);
                }),
            DateFilter::make('To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('login_time', '<=', $value);
                })

        ];
    }

}
