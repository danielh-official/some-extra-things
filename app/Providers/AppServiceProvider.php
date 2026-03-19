<?php

namespace App\Providers;

use App\Models\SmartList;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Native\Desktop\Facades\Settings;

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
            try {
                $theme = Settings::get('theme', 'system');
            } catch (Exception) {
                $theme = session('theme', 'system');
            }

            $view->with([
                'sidebarPinnedSmartLists' => SmartList::where('is_pinned_to_sidebar', true)->orderBy('name')->get(),
                'theme' => $theme,
            ]);
        });
    }
}
