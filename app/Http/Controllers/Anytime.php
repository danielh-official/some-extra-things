<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class Anytime extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::where('status', 'Open')
            ->where('start', 'anytime')
            ->where('is_inbox', false)
            ->orderBy('creation_date', 'desc')
            ->get();

        return view('anytime', compact('items'));
    }
}
