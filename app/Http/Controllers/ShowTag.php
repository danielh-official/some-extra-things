<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Native\Desktop\Facades\Settings;

class ShowTag extends Controller
{
    public function __invoke(Request $request, string $tag): View
    {
        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $all = Item::notTrashed()
            ->where('status', 'Open')
            ->whereHas('tags', fn ($q) => $q->where('tags.id', $tagModel->id))
            ->orderBy('type')
            ->orderBy('creation_date')
            ->get();

        $items = $all->filter(fn (Item $item) => $item->start !== 'Someday' && ! ($item->start_date && $item->start_date->gt(today()))
        )->groupBy('type');

        $upcomingItems = $all->filter(fn (Item $item) => $item->start !== 'Someday' && $item->start_date && $item->start_date->gt(today())
        )->groupBy('type');

        $somedayItems = $all->filter(fn (Item $item) => $item->start === 'Someday'
        )->groupBy('type');

        try {
            $allowTagEdits = Settings::get('allow_tag_edits', false);
        } catch (Exception) {
            $allowTagEdits = session('allow_tag_edits', false);
        }

        return view('tags.show', compact('tagModel', 'items', 'upcomingItems', 'somedayItems', 'allowTagEdits'));
    }
}
