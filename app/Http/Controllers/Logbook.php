<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class Logbook extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $grouped = Item::where('is_logged', true)
            ->orderBy('completion_date', 'desc')
            ->get()
            ->groupBy(fn (Item $item) => $item->completion_date?->toDateString() ?? 'Unknown')
            ->sortKeysDesc();

        return view('logbook', compact('grouped'));
    }
}
