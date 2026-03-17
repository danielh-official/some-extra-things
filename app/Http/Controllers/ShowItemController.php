<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowItemController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Item $item): View
    {
        return view('items.show', compact('item'));
    }
}
