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
            return in_array($user->user_type_id, [1]);
        });

        Gate::define('can-export-ticket', function (User $user) {
            return in_array($user->user_type_id, [1,2,3]);
        });

        // Gate::define('live-dashboard-user', function (User $user) {
        //     return in_array($user->user_type_id, [8]);
        // });

        Gate::define('can-see-user-details', function (User $user) {
            return in_array($user->user_type_id, [1,2,3]);
        });

        Gate::define('can-view-tickets', function (User $user) {
            return in_array($user->user_type_id, [1,2,3,4,9]);
        });

        Gate::define('can-view-chat', function (User $user) {
            return in_array($user->user_type_id, [1,2,3,4,9]);
        });

        Gate::define('nps-user', function (User $user) {
            return in_array($user->user_type_id, [12]);
        });

        Gate::define('can-view-service-tickets', function (User $user) {
            return in_array($user->user_type_id, [1,2,3,4,9,12]);
        });

        Gate::define('can-view-cdr-reports', function (User $user) {
            return in_array($user->user_type_id, [1,8,12]);
        });



    }

    

    // public function boot()
    // {
    //     $this->registerPolicies();

    //     Gate::define('is-super-admin', function (User $user) {
    //         $allowedRoles = [1];
    //         $allowedTenants = ['hutch', 'internal']; // or add any tenant here
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('is-admin', function (User $user) {
    //         $allowedRoles = [1, 2];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('is-supervisor', function (User $user) {
    //         $allowedRoles = [2];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('is-agent', function (User $user) {
    //         $allowedRoles = [3, 4];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('is-has-outlet', function (User $user) {
    //         $allowedRoles = [5, 6];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('outlet-user', function (User $user) {
    //         $allowedRoles = [6];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('client-admin', function (User $user) {
    //         $allowedRoles = [7];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-leads', function (User $user) {
    //         $allowedRoles = [1, 2, 3, 4];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-service-tickets', function (User $user) {
    //         $allowedRoles = [1, 2, 3, 4];
    //         $allowedTenants = ['hutch', 'internal','microsoft'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-reports-tab', function (User $user) {
    //         $allowedRoles = [1, 8];
    //         $allowedTenants = ['internal','microsoft']; 
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-reports', function (User $user) {
    //         $allowedRoles = [1, 8];
    //         $allowedTenants = ['internal']; 
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-cdr-reports', function (User $user) {
    //         $allowedRoles = [1, 8];
    //         $allowedTenants = ['microsoft']; 
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-export-ticket', function (User $user) {
    //         $allowedRoles = [1, 2, 3];
    //         $allowedTenants = ['internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('live-dashboard-user', function (User $user) {
    //         $allowedRoles = [8];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-see-user-details', function (User $user) {
    //         $allowedRoles = [1, 2, 3];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-tickets', function (User $user) {
    //         $allowedRoles = [1, 2, 3, 4, 9];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });

    //     Gate::define('can-view-chat', function (User $user) {
    //         $allowedRoles = [1, 2, 3, 4, 9];
    //         $allowedTenants = ['hutch', 'internal'];
    //         return checkRoleAndTenant($user, $allowedRoles, $allowedTenants);
    //     });


    //     // Helper function to keep code DRY
    //     function checkRoleAndTenant(User $user, array $allowedRoles, array $allowedTenants): bool
    //     {
    //         if (!in_array($user->user_type_id, $allowedRoles)) {
    //             return false;
    //         }

    //         $tenant = strtolower($user->tenant_context ?? '');

    //         foreach ($allowedTenants as $allowedTenant) {
    //             if (str_contains($tenant, $allowedTenant)) {
    //                 return true;
    //             }
    //         }

    //         return false;
    //     }




    // }
}
