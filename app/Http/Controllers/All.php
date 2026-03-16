<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;

class All extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $grouped = Item::where('status', 'Open')
            ->orderBy('creation_date', 'desc')
            ->get()
            ->groupBy('parent');

        $tags = Tag::orderBy('name')->get();

        return view('all', compact('grouped', 'tags'));
    }
}
