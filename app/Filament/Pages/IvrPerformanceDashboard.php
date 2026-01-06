<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\AuIvrCall;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class IvrPerformanceDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $slug = 'ivr-performance-dashboard';
    // protected static ?string $title = 'IVR Performance';
    protected static ?string $navigationLabel = 'IVR Performance';
    protected static ?string $navigationGroup = 'Reports';

    protected static string $view = 'filament.pages.ivr-performance-dashboard';

    protected function getHeading(): string
    {
        return '';
    }

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
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
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
             $this->startDate = Carbon::parse($this->endDate)->subDays(30)->toDateString();
        }
    }

    protected function getViewData(): array
    {
        $startDate = $this->startDate ?? now()->subDays(30)->toDateString();
        $endDate = $this->endDate ?? now()->toDateString();

        $dnisList = AuIvrCall::query()
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->distinct('dnis')
            ->pluck('dnis')
            ->filter()
            ->toArray();

        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
            'dnisList' => $dnisList,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // \App\Filament\Widgets\IvrComparisonChart::class,
        ];
    }
}
