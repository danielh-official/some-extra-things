<?php

namespace App\Providers;

use App\Models\SmartList;
use Illuminate\Support\Facades\View;
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
        View::composer('components.layouts.app', function ($view) {
            $view->with([
                'sidebarSmartLists' => SmartList::orderBy('name')->get(),
            ]);
        });
    }
}
