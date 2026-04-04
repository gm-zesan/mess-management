<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

// Models
use App\Models\User;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Deposit;
use App\Models\Month;
use App\Models\Mess;
use App\Models\MessUser;

// Policies
use App\Policies\UserPolicy;
use App\Policies\MealPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\DepositPolicy;
use App\Policies\MonthPolicy;
use App\Policies\MessPolicy;
use App\Policies\MessUserPolicy;

// Enums
use App\Enums\RoleEnum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Meal::class, MealPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(Deposit::class, DepositPolicy::class);
        Gate::policy(Month::class, MonthPolicy::class);
        Gate::policy(Mess::class, MessPolicy::class);
        Gate::policy(MessUser::class, MessUserPolicy::class);

        // Super Admin access control - Grant all abilities to superadmin
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole(RoleEnum::SUPERADMIN) ? true : null;
        });

        // Use Bootstrap for pagination
        Paginator::useBootstrapFive();
    }
}
