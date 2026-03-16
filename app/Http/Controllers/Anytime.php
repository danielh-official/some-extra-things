<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Anytime extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $items = Item::notTrashed()
            ->where('status', 'Open')
            ->where(function (Builder $query) {
                $query->where('start', 'Anytime')
                    ->orWhere('start_date', '<=', today());
            })
            ->where('is_inbox', false)
            ->orderBy('creation_date', 'desc')
            ->get();

        return view('anytime', compact('items'));
    }
}
