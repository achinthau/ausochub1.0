<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class TechnicianPerformanceDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $slug = 'technician-performance-dashboard';
    protected static ?string $title = 'Technician Performance';
    protected static ?string $navigationLabel = 'Technician Performance';
    protected static bool $shouldRegisterNavigation = false; // Hidden from main nav, accessed via button

    protected static string $view = 'filament.pages.technician-performance-dashboard';

    public $technician_id;
    public $technicianName;
    public $startDate;
    public $endDate;
    public $minDate;
    public $maxDate;

    protected $queryString = [
        'startDate',
        'endDate',
    ];

    public function mount()
    {
        $this->technicianName = request()->query('technician');
        $this->technician_id = request()->query('technician_id');

        if (!$this->technicianName && $this->technician_id) {
             $user = User::find($this->technician_id);
             $this->technicianName = $user?->name;
        }

        // Setup min/max dates (last 30 days)
        $this->maxDate = now()->toDateString();
        $this->minDate = now()->subDays(30)->toDateString();

        // Get dates from query or default
        $this->startDate = request()->query('startDate', $this->minDate);
        $this->endDate = request()->query('endDate', $this->maxDate);

        // Clamp dates to last 30 days range
        if ($this->startDate < $this->minDate) $this->startDate = $this->minDate;
        if ($this->startDate > $this->maxDate) $this->startDate = $this->maxDate;
        if ($this->endDate < $this->minDate) $this->endDate = $this->minDate;
        if ($this->endDate > $this->maxDate) $this->endDate = $this->maxDate;
        
        // Ensure start is not after end
        if ($this->startDate > $this->endDate) {
            $this->startDate = $this->endDate;
        }
    }

    protected function getViewData(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\TechnicianRatingChart::class,
            \App\Filament\Widgets\TechnicianTicketCountBarChart::class,
            // \App\Filament\Widgets\TechnicianComparisonTable::class,
        ];
    }

}
