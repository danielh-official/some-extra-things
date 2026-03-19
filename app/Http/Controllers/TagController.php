<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Item;
use App\Models\Tag;
use App\Services\TagService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Native\Desktop\Facades\Settings;

class TagController extends Controller
{
    public function __construct(protected TagService $tagService) {}

    public function index(Request $request)
    {
        $sort = $request->input('sort', $request->session()->get('tags_sort', 'name'));

        $request->session()->put('tags_sort', $sort);

        $tags = Tag::withCount(['items' => fn ($q) => $q->where('status', 'Open')->where('is_trashed', false)])
            ->when($sort === 'count_desc', fn ($q) => $q->orderByDesc('items_count')->orderBy('name'))
            ->when($sort !== 'count_desc', fn ($q) => $q->orderBy('name'))
            ->get();

        return view('tags', compact('tags', 'sort'));
    }

    public function show(Request $request, string $tag): View
    {
        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $invert = $request->boolean('invert');

        $tagScope = $invert
            ? 'whereDoesntHave'
            : 'whereHas';

        $all = Item::notTrashed()
            ->where('status', 'Open')
            ->$tagScope('tags', fn ($q) => $q->where('tags.id', $tagModel->id))
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

        return view('tags.show', compact('tagModel', 'items', 'upcomingItems', 'somedayItems', 'allowTagEdits', 'invert'));
    }

    public function edit(string $tag): View
    {
        try {
            $allowTagEdits = Settings::get('allow_tag_edits', false);
        } catch (Exception) {
            $allowTagEdits = session('allow_tag_edits', false);
        }

        abort_if(! $allowTagEdits, 404);

        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $otherTags = Tag::where('id', '!=', $tagModel->id)->orderBy('name')->get();

        return view('tags.edit', compact('tagModel', 'otherTags'));
    }

    public function update(UpdateTagRequest $request, string $tag): RedirectResponse
    {
        try {
            $allowTagEdits = Settings::get('allow_tag_edits', false);
        } catch (Exception) {
            $allowTagEdits = session('allow_tag_edits', false);
        }

        abort_if(! $allowTagEdits, 404);

        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $newName = $request->validated('name');
        $newParentId = $request->validated('parent_tag_id') ?: null;

        $nameChanged = $newName !== $tagModel->name;
        $parentChanged = (string) $newParentId !== (string) ($tagModel->parent_tag_id ?? '');

        if ($nameChanged || $parentChanged) {
            try {
                $this->tagService->update($tagModel, $newName, $newParentId);
            } catch (Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['things' => 'Failed to update tag in Things 3: '.$e->getMessage()]);
            }
        }

        $updateData = ['name' => $newName, 'parent_tag_id' => $newParentId];

        if ($newParentId) {
            $parentTag = Tag::find($newParentId);
            $updateData['parent_things_tag_id'] = $parentTag?->things_id;
        } else {
            $updateData['parent_things_tag_id'] = null;
        }

        $tagModel->update($updateData);

        return redirect()
            ->route('tags.show', $tagModel->things_id ?? $tagModel->id)
            ->with('status', 'Tag updated.');
    }
}
