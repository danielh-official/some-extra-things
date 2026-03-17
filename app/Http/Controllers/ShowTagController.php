<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShowTagController extends Controller
{
    public function __invoke(Request $request, string $tag): View
    {
        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $items = Item::notTrashed()
            ->whereHas('tags', fn ($q) => $q->where('tags.id', $tagModel->id))
            ->orderBy('type')
            ->orderBy('creation_date')
            ->get()
            ->groupBy('type');

        return view('tags.show', compact('tagModel', 'items'));
    }
}
