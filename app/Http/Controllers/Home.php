<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;

class Home extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::where('status', 'Open')->orderBy('creation_date', 'desc')->get();

        $tags = Tag::orderBy('name')->get();

        return view('home', compact('items', 'tags'));
    }
}
