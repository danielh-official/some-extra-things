<?php

namespace App\Services;

use App\Models\Tag;

class TagService
{
    public function import(): string
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

        return shell_exec('osascript -e '.escapeshellarg($script));
    }

    public function update(Tag $tag, string $newName, ?string $newParentId): void
    {
        $script = $this->buildAppleScript($tag, $newName, $newParentId);

        $output = [];
        $returnCode = 0;
        exec('osascript -e '.escapeshellarg($script).' 2>&1', $output, $returnCode);

        throw_if($returnCode !== 0, json_encode($output));
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
