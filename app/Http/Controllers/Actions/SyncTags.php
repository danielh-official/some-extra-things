<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;

class SyncTags extends Controller
{
    public function __invoke(TagService $tagService): RedirectResponse
    {
        $result = $tagService->import();

        if (! $result) {
            return redirect()->route('tags.index')->with('error', 'Failed to fetch tags from Things 3.');
        }

        $entries = array_filter(explode('~~~', trim($result)));

        // First pass: upsert all tags without parent_tag_id
        $thingsIdToId = [];
        foreach ($entries as $entry) {
            $parts = explode('|||', $entry);
            if (count($parts) < 2) {
                continue;
            }

            [$thingsId, $name] = $parts;
            $keyboardShortcut = $parts[2] ?? null;

            if ($keyboardShortcut === 'missing value') {
                $keyboardShortcut = null;
            }

            $tag = Tag::updateOrCreate(
                ['name' => $name],
                [
                    'things_id' => $thingsId,
                    'keyboard_shortcut' => $keyboardShortcut ?: null,
                ]
            );

            $thingsIdToId[$thingsId] = $tag->id;
        }

        // Second pass: set parent_tag_id
        foreach ($entries as $entry) {
            $parts = explode('|||', $entry);
            if (count($parts) < 4) {
                continue;
            }

            [$thingsId, , , $parentThingsId] = $parts;
            $parentThingsId = trim($parentThingsId);

            if ($parentThingsId && isset($thingsIdToId[$thingsId])) {
                $update = ['parent_things_tag_id' => $parentThingsId];
                if (isset($thingsIdToId[$parentThingsId])) {
                    $update['parent_tag_id'] = $thingsIdToId[$parentThingsId];
                }
                Tag::where('id', $thingsIdToId[$thingsId])->update($update);
            }
        }

        return redirect()->route('tags.index')->with('status', 'Tags synced from Things 3.');
    }
}
