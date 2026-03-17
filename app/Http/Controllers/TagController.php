<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function edit(string $tag): View
    {
        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $otherTags = Tag::where('id', '!=', $tagModel->id)->orderBy('name')->get();

        return view('tags.edit', compact('tagModel', 'otherTags'));
    }

    public function update(UpdateTagRequest $request, string $tag): RedirectResponse
    {
        $tagModel = Tag::where('id', $tag)->orWhere('things_id', $tag)->firstOrFail();

        $newName = $request->validated('name');
        $newParentId = $request->validated('parent_tag_id') ?: null;

        $nameChanged = $newName !== $tagModel->name;
        $parentChanged = (string) $newParentId !== (string) ($tagModel->parent_tag_id ?? '');

        if ($nameChanged || $parentChanged) {
            $script = $this->buildAppleScript($tagModel, $newName, $newParentId);

            $output = [];
            $returnCode = 0;
            exec('osascript -e '.escapeshellarg($script).' 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['things' => 'Failed to update tag in Things 3: '.implode(' ', $output)]);
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

    protected function buildAppleScript(Tag $tagModel, string $newName, ?string $newParentId): string
    {
        $currentName = $this->asString($tagModel->name);
        $escapedNew = $this->asString($newName);

        $lines = ['tell application "Things3"'];

        if ($newName !== $tagModel->name) {
            $lines[] = "  set name of tag {$currentName} to {$escapedNew}";
        }

        // Name to reference in subsequent statements (after any rename)
        $refName = $newName !== $tagModel->name ? $escapedNew : $currentName;

        if ($newParentId) {
            $parentTag = Tag::find($newParentId);
            if ($parentTag) {
                $escapedParent = $this->asString($parentTag->name);
                $lines[] = "  set parent tag of tag {$refName} to tag {$escapedParent}";
            }
        } elseif ($tagModel->parent_tag_id) {
            $lines[] = "  set parent tag of tag {$refName} to missing value";
        }

        $lines[] = 'end tell';

        return implode("\n", $lines);
    }

    /**
     * Encode a PHP string as a safe AppleScript string literal,
     * handling embedded double-quotes via the AppleScript `quote` constant.
     */
    protected function asString(string $value): string
    {
        $parts = explode('"', $value);

        if (count($parts) === 1) {
            return '"'.$value.'"';
        }

        $segments = array_map(fn (string $p) => '"'.$p.'"', $parts);

        return implode(' & quote & ', $segments);
    }
}
