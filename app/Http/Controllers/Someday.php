<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class Someday extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $projectIds = Item::where('type', 'Project')->pluck('id');

        $grouped = Item::notTrashed()
            ->whereNotIn('type', ['Area', 'Heading'])
            ->where('status', 'Open')
            ->where('start', 'Someday')
            ->where(function ($q) use ($projectIds) {
                $q->where('type', '!=', 'To-Do')
                    ->orWhereNull('parent_id')
                    ->orWhereNotIn('parent_id', $projectIds);
            })
            ->orderBy('creation_date', 'desc')
            ->get()
            ->groupBy(fn (Item $item) => $item->parent ?? '');

        return view('someday', compact('grouped'));
    }
}
