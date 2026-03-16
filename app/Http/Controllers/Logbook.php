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
        $items = Item::where('is_logged', true)
            ->orderBy('completion_date', 'desc')
            ->get();

        return view('logbook', compact('items'));
    }
}
