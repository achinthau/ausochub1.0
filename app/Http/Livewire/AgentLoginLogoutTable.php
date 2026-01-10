<?php

namespace App\Http\Livewire;

use App\Exports\AgentLoginLogoutExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\AgentLogin;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateTimeFilter;

class AgentLoginLogoutTable extends DataTableComponent
{
    protected $model = AgentLogin::class;

    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
        ->setDefaultSort('login_time', 'desc');

        $this->setBulkActions([
            'exportSelected' => 'Export',
        ]);
    }

    public function query()
    {
        return AgentLogin::with('user');
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

    $agentLoginLogout = AgentLogin::whereIn('id', $selectedIds)->get()->map(function ($item) {
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
            DateTimeFilter::make('From')
                ->config([
                    // Optional: Specify format or range limits
                    'enableTime' => true, // Enables time selection
                    'time_24hr' => true, // Optional: Use 24-hour format
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('login_time', '>=', $value);
                }),
    
            DateTimeFilter::make('To')
                ->config([
                    'enableTime' => true, // Enables time selection
                    'time_24hr' => true, // Optional: Use 24-hour format
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('login_time', '<=', $value);
                }),
        ];
    }

}
