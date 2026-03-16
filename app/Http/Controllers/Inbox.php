<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class Inbox extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::where('is_inbox', true)
            ->where('status', 'Open')
            ->orderBy('creation_date', 'desc')
            ->get();

        return view('inbox', compact('items'));
    }
}
