<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;

class ShowItemController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Item $item): View|RedirectResponse
    {
        if ($item->type === 'Heading') {
            $parent = $item->parent_id ? Item::find($item->parent_id) : null;
            if ($parent) {
                return redirect()->route('items.show', $parent);
            }

            abort(404);
        }

        $notesHtml = null;
        if ($item->notes) {
            $converter = new CommonMarkConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);
            $notesHtml = $converter->convert($item->notes)->getContent();
        }

        $childTodos = null;
        if ($item->type === 'Project') {
            $childTodos = Item::notTrashed()
                ->where('parent_id', $item->id)
                ->where('type', 'To-Do')
                ->where('status', 'Open')
                ->orderBy('creation_date')
                ->get()
                ->groupBy(fn (Item $child) => $child->heading ?? '');
        }

        return view('items.show', compact('item', 'notesHtml', 'childTodos'));
    }
}
