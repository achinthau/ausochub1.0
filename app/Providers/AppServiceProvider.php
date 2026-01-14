<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerQueryBuilderMacros();

        \Filament\Facades\Filament::registerRenderHook(
            'body.start',
            fn (): string => $this->renderDashboardBackButton(),
        );
    }

    protected function renderDashboardBackButton(): string
    {
        $routeName = request()->route()?->getName();
        
        if ($routeName === 'filament.pages.ivr-performance-dashboard') {
            return view('filament.components.back-button', [
                'url' => route('reports.ivr-detail'),
                'label' => 'Back'
            ])->render();
        }

        if ($routeName === 'filament.pages.technician-performance-dashboard') {
            return view('filament.components.back-button', [
                'url' => route('cx-tickets.index'),
                'label' => 'Back'
            ])->render();
        }

        if ($routeName === 'filament.pages.daily-call-summary-dashboard') {
            return view('filament.components.back-button', [
                'url' => route('reports.daily-calls-summary-report'),
                'label' => 'Back'
            ])->render();
        }

        if ($routeName === 'filament.pages.daily-queue-summary-dashboard') {
            return view('filament.components.back-button', [
                'url' => route('reports.daily-queue-summary-report'),
                'label' => 'Back'
            ])->render();
        }

        return '';
    }


    protected function registerQueryBuilderMacros()
    {
        Builder::macro('addSubSelect', function ($column, $query) {
            if (is_null($this->columns)) {
                $this->select($this->from . '.*');
            }

            return $this->selectSub($query, $column);
        });

        Builder::macro('orderBySub', function ($query, $direction = 'asc') {
            list($query, $bindings) = $this->createSub($query);

            return $this->addBinding($bindings, 'order')->orderBy(DB::raw('(' . $query . ')'), $direction);
        });
    }
}
