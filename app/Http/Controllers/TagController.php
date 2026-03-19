<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use App\Services\TagService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Native\Desktop\Facades\Settings;

class TagController extends Controller
{
    public function __construct(protected TagService $tagService) {}

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
