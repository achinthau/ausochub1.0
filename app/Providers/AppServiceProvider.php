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
