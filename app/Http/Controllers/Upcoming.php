<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class Upcoming extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::where('status', 'Open')
            ->where(function ($query) {
                $query->where('start_date', '>', today())
                    ->orWhere(function ($query) {
                        $query->whereNull('start_date')
                            ->whereNotNull('deadline')
                            ->where('deadline', '>', today());
                    });
            })
            ->orderBy('start_date')
            ->orderBy('deadline')
            ->get();

        $grouped = $items
            ->groupBy(fn (Item $item) => ($item->start_date ?? $item->deadline)->toDateString())
            ->sortKeys();

        return view('upcoming', compact('grouped'));
    }
}
