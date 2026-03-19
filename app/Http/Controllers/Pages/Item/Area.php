<?php

namespace App\Http\Controllers\Pages\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class Area extends Controller
{
    public function __invoke(Request $request, Item $area): View
    {
        abort_if($area->type !== 'Area', 404);

        $all = Item::notTrashed()
            ->whereIn('type', ['Project', 'To-Do'])
            ->where('parent_id', $area->id)
            ->where('status', 'Open')
            ->orderBy('creation_date')
            ->get();

        $isUpcoming = fn (Item $item): bool => $item->start !== 'Someday' && $item->start_date && $item->start_date->gt(today());

        $isSomeday = fn (Item $item): bool => $item->start === 'Someday';

        $items = $all->reject(fn (Item $item) => $isUpcoming($item) || $isSomeday($item));
        $upcomingItems = $all->filter($isUpcoming);
        $somedayItems = $all->filter($isSomeday);

        return view('item.area', compact('area', 'items', 'upcomingItems', 'somedayItems'));
    }
}
