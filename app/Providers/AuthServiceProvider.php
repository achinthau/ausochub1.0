<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('is-super-admin', function (User $user) {
            return $user->user_type_id === 1;
        });

        Gate::define('is-admin', function (User $user) {
            return in_array($user->user_type_id, [1, 2]);
        });

        Gate::define('is-supervisor', function (User $user) {
            return $user->user_type_id === 2;
        });

        Gate::define('is-agent', function (User $user) {
            return in_array($user->user_type_id, [3, 4]);
        });

        Gate::define('is-has-outlet', function (User $user) {
            return in_array($user->user_type_id, [5, 6]);
        });

        Gate::define('outlet-user', function (User $user) {
            return $user->user_type_id === 6;
        });

        Gate::define('client-admin', function (User $user) {
            return $user->user_type_id === 7;
        });

        Gate::define('can-view-leads', function (User $user) {
            return in_array($user->user_type_id, [1,2,3,4]);
        });
        Gate::define('can-view-reports', function (User $user) {
            return in_array($user->user_type_id, [1,8]);
        });

        Gate::define('can-export-ticket', function (User $user) {
            return in_array($user->user_type_id, [1,2,3]);
        });

    }
}
