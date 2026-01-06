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
            // \App\Filament\Widgets\IvrComparisonChart::class,
        ];
    }
}
