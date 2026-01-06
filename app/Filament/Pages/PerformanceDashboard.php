<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PerformanceDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $slug = 'performance-dashboard';
    protected static ?string $title = 'Performance Dashboard';

    protected static string $view = 'filament.pages.performance-dashboard';

    public $startDate;
    public $endDate;

    public function mount()
    {
        if (! auth()->user()->can('is-admin')) {
            abort(403);
        }
        $this->startDate = request()->query('startDate', now()->startOfMonth()->toDateString());
        $this->endDate = request()->query('endDate', now()->endOfMonth()->toDateString());
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
