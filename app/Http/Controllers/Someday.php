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
        $grouped = Item::notTrashed()->where('status', 'Open')
            ->where('start', 'Someday')
            ->orderBy('creation_date', 'desc')
            ->get()
            ->groupBy(fn (Item $item) => $item->parent ?? '');

        return view('someday', compact('grouped'));
    }
}
