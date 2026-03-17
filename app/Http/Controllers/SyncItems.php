<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;

class SyncItems extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $script = <<<'APPLESCRIPT'
        tell application "Things3"
            set output to ""

            repeat with a in areas
                set output to output & "ITEM|||Area|||" & (id of a) & "|||" & (name of a) & "~~~"
            end repeat

            repeat with p in projects
                set pid to id of p
                set pstatus to status of p as string
                set pparent to ""
                try
                    set pparent to id of area of p
                end try
                set pnotes to ""
                try
                    set pnotes to notes of p
                end try
                set output to output & "ITEM|||Project|||" & pid & "|||" & (name of p) & "|||" & pparent & "|||" & pstatus & "|||" & pnotes & "|||" & (creation date of p as string) & "|||" & (last modified date of p as string) & "~~~"
            end repeat

            repeat with t in to dos
                set tid to id of t
                set tstatus to status of t as string
                set tparent to ""
                try
                    set tparent to id of project of t
                end try
                if tparent is "" then
                    try
                        set tparent to id of area of t
                    end try
                end if
                set tnotes to ""
                try
                    set tnotes to notes of t
                end try
                set tisinbox to "No"
                if list name of t is "Inbox" then
                    set tisinbox to "Yes"
                end if
                set ttags to ""
                repeat with tg in tags of t
                    set ttags to ttags & (name of tg) & ","
                end repeat
                set output to output & "ITEM|||To-Do|||" & tid & "|||" & (name of t) & "|||" & tparent & "|||" & tstatus & "|||" & tnotes & "|||" & (creation date of t as string) & "|||" & (last modified date of t as string) & "|||" & tisinbox & "|||" & ttags & "~~~"
            end repeat

            return output
        end tell
        APPLESCRIPT;

        $result = shell_exec('osascript -e '.escapeshellarg($script));

        if (! $result) {
            return redirect()->back()->with('sync_error', 'Failed to fetch data from Things 3.');
        }

        $entries = array_filter(array_map('trim', explode('~~~', trim($result))));
        $synced = 0;

        foreach ($entries as $entry) {
            if (! str_starts_with($entry, 'ITEM|||')) {
                continue;
            }

            $parts = explode('|||', $entry);
            $type = $parts[1] ?? null;
            $id = trim($parts[2] ?? '');
            $title = trim($parts[3] ?? '');

            if (! $id || ! $title || ! $type) {
                continue;
            }

            $parentId = trim($parts[4] ?? '') ?: null;
            $rawStatus = strtolower(trim($parts[5] ?? 'open'));
            $statusMap = ['open' => 'Open', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
            $status = $statusMap[$rawStatus] ?? 'Open';
            $isLogged = in_array($status, ['Completed', 'Cancelled']);

            $data = [
                'type' => $type,
                'title' => $title,
                'parent_id' => $parentId,
                'status' => $status,
                'is_logged' => $isLogged,
                'is_trashed' => false,
            ];

            if ($type === 'Area') {
                $data['status'] = null;
                $data['is_logged'] = false;
                $data['parent_id'] = null;
            }

            if (in_array($type, ['Project', 'To-Do'])) {
                $data['notes'] = trim($parts[6] ?? '') ?: null;
            }

            // Resolve parent name
            if ($parentId) {
                $parentItem = Item::find($parentId);
                if ($parentItem) {
                    $data['parent'] = $parentItem->title;
                }
            }

            if ($type === 'To-Do') {
                $data['is_inbox'] = trim($parts[9] ?? 'No') === 'Yes';
                $rawTags = array_filter(explode(',', trim($parts[10] ?? '')));
                $data['tags'] = array_values($rawTags);
            }

            $item = Item::updateOrCreate(['id' => $id], array_filter($data, fn ($v) => $v !== null));

            if ($type === 'To-Do' && ! empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $item->tags()->sync($tagIds);
            }

            $synced++;
        }

        return redirect()->back()->with('sync_status', "Synced {$synced} items from Things 3.");
    }
}
