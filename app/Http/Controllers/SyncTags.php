<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\RedirectResponse;

class SyncTags extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $script = <<<'APPLESCRIPT'
        tell application "Things3"
            set tagList to tags
            set output to ""
            repeat with t in tagList
                set tid to id of t
                set tname to name of t
                set tshortcut to ""
                try
                    set tshortcut to keyboard shortcut of t
                end try
                set tparent to ""
                try
                    set tparent to id of parent tag of t
                end try
                set output to output & tid & "|||" & tname & "|||" & tshortcut & "|||" & tparent & "~~~"
            end repeat
            return output
        end tell
        APPLESCRIPT;

        $result = shell_exec('osascript -e '.escapeshellarg($script));

        if (! $result) {
            return redirect()->route('tags')->with('error', 'Failed to fetch tags from Things 3.');
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

        return redirect()->route('tags')->with('status', 'Tags synced from Things 3.');
    }
}
