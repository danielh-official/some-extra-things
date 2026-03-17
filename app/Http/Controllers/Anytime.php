<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Anytime extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::notTrashed()
            ->topLevel()
            ->where('status', 'Open')
            ->where(function (Builder $query) {
                $query->whereNull('start')
                    ->orWhere('start_date', '<=', today());
            })
            ->where('is_inbox', false)
            ->orderBy('creation_date', 'desc')
            ->get();

        $grouped = $items->groupBy(fn (Item $item) => $item->parent ?? '');

        return view('anytime', compact('grouped'));
    }
}
