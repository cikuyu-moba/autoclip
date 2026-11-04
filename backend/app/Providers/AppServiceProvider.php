<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\Mail::extend('gmail_api', function (array $config) {
            return new \App\Mail\Transport\GmailApiTransport();
        });

        if (Schema::hasTable('menus')) {
            $menus = Menu::root()->with('submenus')->get();
            View::share('navigation_menus', $menus);
        } else {
            View::share('navigation_menus', collect());
        }

        if (Schema::hasTable('visitors')) {
            // Count unique visitors purely from the database
            $todayCount = \App\Models\Visitor::whereDate('date', now()->toDateString())->count();
            $weeklyCount = \App\Models\Visitor::whereDate('date', '>=', now()->startOfWeek()->toDateString())->count();
            $monthlyCount = \App\Models\Visitor::whereDate('date', '>=', now()->startOfMonth()->toDateString())->count();
            $yearlyCount = \App\Models\Visitor::whereDate('date', '>=', now()->startOfYear()->toDateString())->count();

            View::share('visitor_stats', [
                'today' => $todayCount,
                'weekly' => $weeklyCount,
                'monthly' => $monthlyCount,
                'yearly' => $yearlyCount,
            ]);
        } else {
            View::share('visitor_stats', [
                'today' => 0,
                'weekly' => 0,
                'monthly' => 0,
                'yearly' => 0,
            ]);
        }
    }
}
