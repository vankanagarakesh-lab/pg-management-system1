<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        view()->composer('*', function ($view) {
            $userId = session('pg_user_id');
            $user = null;
            $role = null;
            $staffRole = null;
            if ($userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $role = $user->role;
                    $staffRole = $user->staff_role;
                }
            }
            
            $defaultTab = 'overview';
            if ($role === 'staff') {
                if ($staffRole === 'Housekeeping') $defaultTab = 'tasks';
                elseif ($staffRole === 'Food Management') $defaultTab = 'food';
                elseif ($staffRole === 'Maintenance') $defaultTab = 'maintenance';
            }

            $notifications = collect();
            if ($user) {
                $notifications = \App\Models\SystemNotification::where('type', $user->role)
                    ->where(function ($query) use ($user) {
                        $query->whereNull('user_id')
                              ->orWhere('user_id', $user->id);
                    })
                    ->latest()
                    ->get();
            }

            $view->with('loggedUser', $user);
            $view->with('userRole', $role);
            $view->with('staffRole', $staffRole);
            $view->with('activeTab', request()->query('tab', $defaultTab));
            $view->with('notifications', $notifications);
        });
    }
}
