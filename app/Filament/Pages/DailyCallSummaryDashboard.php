<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Carbon\Carbon;

class DailyCallSummaryDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-report';

    protected static string $view = 'filament.pages.daily-call-summary-dashboard';

    protected static bool $shouldRegisterNavigation = false;

    public $startDate;
    public $endDate;
    public $maxDate;
    public $minDate;

    public function mount()
    {
        $this->maxDate = Carbon::now()->format('Y-m-d');
        $this->minDate = Carbon::now()->subYears(2)->format('Y-m-d');

        $this->endDate = request()->query('endDate', Carbon::now()->format('Y-m-d'));
        $this->startDate = request()->query('startDate', Carbon::parse($this->endDate)->subDays(30)->format('Y-m-d'));

        // Clamp dates
        if ($this->startDate < $this->minDate) {
            $this->startDate = $this->minDate;
            $this->endDate = Carbon::parse($this->startDate)->addDays(30)->format('Y-m-d');
        }

        if ($this->endDate > $this->maxDate) {
            $this->endDate = $this->maxDate;
            $this->startDate = Carbon::parse($this->endDate)->subDays(30)->format('Y-m-d');
        }
    }

    public function updatedStartDate($value)
    {
        $this->startDate = $value;
        $this->endDate = Carbon::parse($value)->addDays(30)->format('Y-m-d');
        
        if ($this->endDate > $this->maxDate) {
            $this->endDate = $this->maxDate;
            $this->startDate = Carbon::parse($this->endDate)->subDays(30)->format('Y-m-d');
        }
    }

    public function updatedEndDate($value)
    {
        $this->endDate = $value;
        $this->startDate = Carbon::parse($value)->subDays(30)->format('Y-m-d');

        if ($this->startDate < $this->minDate) {
            $this->startDate = $this->minDate;
            $this->endDate = Carbon::parse($this->startDate)->addDays(30)->format('Y-m-d');
        }
    }

    protected function getViewData(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }
}
