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
            ->whereNotNull('start_date')
            ->where('start_date', '>', today())
            ->orderBy('start_date')
            ->get();

        return view('upcoming', compact('items'));
    }
}
