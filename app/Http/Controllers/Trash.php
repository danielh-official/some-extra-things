<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class Trash extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::where('status', 'Cancelled')
            ->orderBy('modification_date', 'desc')
            ->get();

        return view('trash', compact('items'));
    }
}
