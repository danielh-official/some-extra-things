<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class Tags extends Controller
{
    public function __invoke(Request $request): View
    {
        $sort = $request->input('sort', $request->session()->get('tags_sort', 'name'));

        $request->session()->put('tags_sort', $sort);

        $tags = Tag::withCount(['items' => fn ($q) => $q->where('status', 'Open')->where('is_trashed', false)])
            ->when($sort === 'count_desc', fn ($q) => $q->orderByDesc('items_count')->orderBy('name'))
            ->when($sort !== 'count_desc', fn ($q) => $q->orderBy('name'))
            ->get();

        return view('tags', compact('tags', 'sort'));
    }
}
