<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LaterProjects extends Controller
{
    public function __invoke(Request $request): View
    {
        $grouped = Item::notTrashed()
            ->where('type', 'Project')
            ->where('status', 'Open')
            ->where(function ($query) {
                $query->where('start', 'Someday')
                    ->orWhere('start_date', '>', today());
            })
            ->orderBy('creation_date')
            ->get()
            ->groupBy(fn (Item $item) => $item->parent ?? '');

        return view('later-projects', compact('grouped'));
    }
}
