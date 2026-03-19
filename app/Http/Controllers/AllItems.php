<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AllItems extends Controller
{
    /**
     * Display all non-trashed to-dos and projects grouped by bucket.
     */
    public function __invoke(Request $request): View
    {
        $kanban = session('all_kanban', 'vertical');

        $items = Item::notTrashed()
            ->whereIn('type', ['To-Do', 'Project'])
            ->get();

        $bucketOrder = ['Inbox', 'Today', 'Anytime', 'Upcoming', 'Someday', 'Logbook'];

        $grouped = $items
            ->sortBy(fn (Item $item) => $item->start_date?->timestamp ?? PHP_INT_MAX)
            ->groupBy(fn (Item $item) => $this->bucket($item))
            ->sortBy(fn ($_, $key) => array_search($key, $bucketOrder));

        return view('all', compact('grouped', 'kanban'));
    }

    /**
     * Toggle the kanban layout stored in the session.
     */
    public function toggleKanban(): RedirectResponse
    {
        $current = session('all_kanban', 'vertical');
        session(['all_kanban' => $current === 'horizontal' ? 'vertical' : 'horizontal']);

        return redirect()->route('all');
    }

    /**
     * Classify an item into a display bucket.
     */
    protected function bucket(Item $item): string
    {
        if ($item->is_inbox) {
            return 'Inbox';
        }

        if ($item->is_logged) {
            return 'Logbook';
        }

        if ($item->start_date && $item->start_date->lte(today())) {
            return 'Today';
        }

        if ($item->start_date && $item->start_date->gt(today())) {
            return 'Upcoming';
        }

        if ($item->start === 'Someday') {
            return 'Someday';
        }

        return 'Anytime';
    }
}
