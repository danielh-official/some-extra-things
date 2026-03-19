<?php

namespace App\Http\Controllers\Pages\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;

class Todo extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Item $item): View|RedirectResponse
    {
        abort_if($item->type !== 'To-Do', 404);

        $notesHtml = null;

        if ($item->notes) {
            $converter = new CommonMarkConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);
            $notesHtml = $converter->convert($item->notes)->getContent();
        }

        $parentItem = $item->parent_id ? Item::find($item->parent_id) : null;

        return view('items.show', compact('item', 'notesHtml', 'parentItem'));
    }
}
