<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class Tags extends Controller
{
    public function __invoke(Request $request): View
    {
        $tags = Tag::withCount('items')->orderBy('name')->get();

        return view('tags', compact('tags'));
    }
}
