<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function show(Request $request, Item $area): View
    {
        abort_if($area->type !== 'Area', 404);

        $projects = Item::notTrashed()
            ->where('parent_id', $area->id)
            ->where('type', 'Project')
            ->where('status', 'Open')
            ->orderBy('creation_date')
            ->get();

        $todos = Item::notTrashed()
            ->where('parent_id', $area->id)
            ->where('type', 'To-Do')
            ->where('status', 'Open')
            ->orderBy('creation_date')
            ->get();

        return view('areas.show', compact('area', 'projects', 'todos'));
    }
}
