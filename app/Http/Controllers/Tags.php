<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class Tags extends Controller
{
    public function __invoke(Request $request): View
    {
        $sort = $request->input('sort', 'name');

        $tags = Tag::withCount('items')
            ->when($sort === 'count_desc', fn ($q) => $q->orderByDesc('items_count')->orderBy('name'))
            ->when($sort !== 'count_desc', fn ($q) => $q->orderBy('name'))
            ->get();

        return view('tags', compact('tags', 'sort'));
    }
}
