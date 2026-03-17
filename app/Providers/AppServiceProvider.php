<?php

namespace App\Providers;

use App\Models\Item;
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
            $activeProjectIds = Item::notTrashed()
                ->where('type', 'Project')
                ->where('status', 'Open')
                ->where(function ($q) {
                    $q->whereNull('start')->orWhere('start', '!=', 'Someday');
                })
                ->where(function ($q) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', today());
                })
                ->pluck('id');

            $areas = Item::where('type', 'Area')
                ->orderBy('creation_date')
                ->get()
                ->map(function (Item $area) use ($activeProjectIds) {
                    $area->sidebarProjects = Item::notTrashed()
                        ->where('type', 'Project')
                        ->where('status', 'Open')
                        ->where('parent_id', $area->id)
                        ->whereIn('id', $activeProjectIds)
                        ->orderBy('creation_date')
                        ->get();

                    return $area;
                });

            $topLevelProjects = Item::notTrashed()
                ->where('type', 'Project')
                ->where('status', 'Open')
                ->whereNull('parent_id')
                ->whereIn('id', $activeProjectIds)
                ->orderBy('creation_date')
                ->get();

            $laterProjectsCount = Item::notTrashed()
                ->where('type', 'Project')
                ->where('status', 'Open')
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('start', 'Someday')->orWhere('start_date', '>', today());
                })
                ->count();

            $view->with([
                'sidebarSmartLists' => SmartList::orderBy('name')->get(),
                'sidebarAreas' => $areas,
                'sidebarTopLevelProjects' => $topLevelProjects,
                'sidebarLaterProjectsCount' => $laterProjectsCount,
            ]);
        });
    }
}
