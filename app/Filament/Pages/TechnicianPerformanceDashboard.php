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

        // Setup max date as today, and min date as 2 years ago
        $this->maxDate = now()->toDateString();
        $this->minDate = now()->subYears(2)->toDateString();

        // Get dates from query
        $reqStartDate = request()->query('startDate');
        $reqEndDate = request()->query('endDate');

        if ($reqStartDate && $reqEndDate) {
            $this->startDate = $reqStartDate;
            $this->endDate = $reqEndDate;

            // Enforce 30-day gap if they deviate
            $start = \Carbon\Carbon::parse($this->startDate);
            $end = \Carbon\Carbon::parse($this->endDate);
            if ($start->diffInDays($end) !== 30) {
                // If the gap is not 30, we default to adjusting the end date based on start date
                $this->endDate = $start->copy()->addDays(30)->toDateString();
            }
        } else {
            // Default to last 30 days
            $this->endDate = $this->maxDate;
            $this->startDate = now()->subDays(30)->toDateString();
        }

        // Clamp to min/max
        if ($this->startDate < $this->minDate) $this->startDate = $this->minDate;
        if ($this->endDate > $this->maxDate) {
             $this->endDate = $this->maxDate;
             $this->startDate = \Carbon\Carbon::parse($this->endDate)->subDays(30)->toDateString();
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
