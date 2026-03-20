<?php

namespace App\Console\Commands;

use App\Services\AppleScriptRunner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportThings extends Command
{
    protected $signature = 'things:export';

    protected $description = 'Export Today list items from Things 3 to things.json via AppleScript';

    public function handle(AppleScriptRunner $runner): int
    {
        $this->info('Connecting to Things 3…');

        [$output, $error, $exitCode] = $runner->run($this->appleScript());

        if ($exitCode !== 0) {
            $this->error("AppleScript failed: {$error}");

            return self::FAILURE;
        }

        $items = json_decode(trim($output), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to parse AppleScript output as JSON: '.json_last_error_msg());

            return self::FAILURE;
        }

        Storage::put('things.json', json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $count = count($items);
        $this->info("Exported {$count} items to things.json.");

        return self::SUCCESS;
    }

    protected function appleScript(): string
    {
        return <<<'APPLESCRIPT'
on escapeStr(str)
    if str is missing value then return ""
    set str to str as string
    set res to ""
    repeat with c in every character of str
        if c is "\"" then
            set res to res & "\\\""
        else if c is "\\" then
            set res to res & "\\\\"
        else if ASCII number of c is 10 then
            set res to res & "\\n"
        else if ASCII number of c is 13 then
            set res to res & "\\r"
        else if ASCII number of c is 9 then
            set res to res & "\\t"
        else
            set res to res & c
        end if
    end repeat
    return res
end escapeStr

on jsonStr(str)
    if str is missing value or str is "" then return "null"
    return "\"" & my escapeStr(str) & "\""
end jsonStr

on jsonDate(d)
    if d is missing value then return "null"
    set y to (year of d) as string
    set mo to ((month of d) as integer) as string
    if length of mo < 2 then set mo to "0" & mo
    set dy to (day of d) as string
    if length of dy < 2 then set dy to "0" & dy
    set h to (hours of d) as string
    if length of h < 2 then set h to "0" & h
    set mi to (minutes of d) as string
    if length of mi < 2 then set mi to "0" & mi
    set sc to (seconds of d) as string
    if length of sc < 2 then set sc to "0" & sc
    return "\"" & y & "-" & mo & "-" & dy & " " & h & ":" & mi & ":" & sc & "\""
end jsonDate

on statusStr(s)
    if s is "open" then return "\"Open\""
    if s is "completed" then return "\"Completed\""
    if s is "canceled" then return "\"Canceled\""
    return "null"
end statusStr

on jsonTags(tagStr)
    if tagStr is missing value or tagStr is "" then return "[]"
    set oldD to AppleScript's text item delimiters
    set AppleScript's text item delimiters to ", "
    set tagList to text items of (tagStr as string)
    set AppleScript's text item delimiters to oldD
    set res to "["
    repeat with i from 1 to count of tagList
        if i > 1 then set res to res & ","
        set res to res & "\"" & my escapeStr(item i of tagList) & "\""
    end repeat
    return res & "]"
end jsonTags

tell application "Things3"
    set jsonParts to {}

    -- Each entry: {list name, start value (JSON), is_inbox (JSON), is_logged (JSON)}
    set listNames to {"Inbox", "Today", "Anytime", "Someday", "Upcoming"}
    set listStarts to {"null", "\"On Date\"", "\"Anytime\"", "\"Someday\"", "\"On Date\""}
    set listIsInbox to {"true", "false", "false", "false", "false"}
    set listIsLogged to {"false", "false", "false", "false", "false"}

    repeat with cfgIdx from 1 to 5
        set listName to item cfgIdx of listNames
        set startVal to item cfgIdx of listStarts
        set isInboxVal to item cfgIdx of listIsInbox
        set isLoggedVal to item cfgIdx of listIsLogged

        set listItems to every to do of list listName
        repeat with i from 1 to count of listItems
            set td to item i of listItems

            -- Detect whether this item is a Project or a To-Do
            set tClassStr to class of td as string
            if tClassStr is "project" then
                set tType to "\"Project\""
            else
                set tType to "\"To-Do\""
            end if

            set tId to my jsonStr(id of td)
            set tTitle to my jsonStr(name of td)
            set tStatusRaw to status of td as string
            set tStatus to my statusStr(tStatusRaw)

            set tNotes to "null"
            try
                set tNotes to my jsonStr(notes of td)
            end try

            set tTags to my jsonTags(tag names of td)

            set tDeadline to "null"
            try
                set tDeadline to my jsonDate(due date of td)
            end try

            set tStartDate to "null"
            try
                set tStartDate to my jsonDate(activation date of td)
            end try

            set tReminder to "null"
            try
                set tReminder to my jsonDate(reminder of td)
            end try

            set tCreation to "null"
            try
                set tCreation to my jsonDate(creation date of td)
            end try

            set tModification to "null"
            try
                set tModification to my jsonDate(modification date of td)
            end try

            set tCompletion to "null"
            try
                set tCompletion to my jsonDate(completion date of td)
            end try

            set tParentId to "null"
            set tParentName to "null"
            try
                set tProj to project of td
                if tProj is not missing value then
                    set tParentId to my jsonStr(id of tProj)
                    set tParentName to my jsonStr(name of tProj)
                end if
            end try
            if tParentId is "null" then
                try
                    set tArea to area of td
                    if tArea is not missing value then
                        set tParentId to my jsonStr(id of tArea)
                        set tParentName to my jsonStr(name of tArea)
                    end if
                end try
            end if

            set tHeadingId to "null"
            set tHeadingName to "null"
            try
                set tHead to heading of td
                if tHead is not missing value then
                    set tHeadingId to my jsonStr(id of tHead)
                    set tHeadingName to my jsonStr(name of tHead)
                end if
            end try

            set tEvening to "false"
            try
                if evening of td then set tEvening to "true"
            end try

            -- Build all_matching_tags: union of own tags + parent project tags + parent area tags
            set allTagsArr to {}
            set ownTagStr to tag names of td
            if ownTagStr is not missing value and (ownTagStr as string) is not "" then
                set oldD to AppleScript's text item delimiters
                set AppleScript's text item delimiters to ", "
                set ownTagItems to text items of (ownTagStr as string)
                set AppleScript's text item delimiters to oldD
                repeat with t in ownTagItems
                    set end of allTagsArr to (t as string)
                end repeat
            end if
            try
                set tProjForTags to project of td
                if tProjForTags is not missing value then
                    set projTagStr to tag names of tProjForTags
                    if projTagStr is not missing value and (projTagStr as string) is not "" then
                        set oldD to AppleScript's text item delimiters
                        set AppleScript's text item delimiters to ", "
                        set projTagItems to text items of (projTagStr as string)
                        set AppleScript's text item delimiters to oldD
                        repeat with t in projTagItems
                            set tStr to t as string
                            if allTagsArr does not contain tStr then
                                set end of allTagsArr to tStr
                            end if
                        end repeat
                    end if
                end if
            end try
            set tAreaForTags to missing value
            try
                set tAreaForTags to area of td
            end try
            if tAreaForTags is missing value then
                try
                    set tProjForArea to project of td
                    if tProjForArea is not missing value then
                        set tAreaForTags to area of tProjForArea
                    end if
                end try
            end if
            if tAreaForTags is not missing value then
                try
                    set areaTagStr to tag names of tAreaForTags
                    if areaTagStr is not missing value and (areaTagStr as string) is not "" then
                        set oldD to AppleScript's text item delimiters
                        set AppleScript's text item delimiters to ", "
                        set areaTagItems to text items of (areaTagStr as string)
                        set AppleScript's text item delimiters to oldD
                        repeat with t in areaTagItems
                            set tStr to t as string
                            if allTagsArr does not contain tStr then
                                set end of allTagsArr to tStr
                            end if
                        end repeat
                    end if
                end try
            end if
            if count of allTagsArr is 0 then
                set tAllTags to "[]"
            else
                set tAllTags to "["
                repeat with j from 1 to count of allTagsArr
                    if j > 1 then set tAllTags to tAllTags & ","
                    set tAllTags to tAllTags & "\"" & my escapeStr(item j of allTagsArr) & "\""
                end repeat
                set tAllTags to tAllTags & "]"
            end if

            set obj to "{\"id\":" & tId
            set obj to obj & ",\"type\":" & tType
            set obj to obj & ",\"title\":" & tTitle
            set obj to obj & ",\"parent\":" & tParentName
            set obj to obj & ",\"parent_id\":" & tParentId
            set obj to obj & ",\"heading\":" & tHeadingName
            set obj to obj & ",\"heading_id\":" & tHeadingId
            set obj to obj & ",\"is_inbox\":" & isInboxVal
            set obj to obj & ",\"start\":" & startVal
            set obj to obj & ",\"start_date\":" & tStartDate
            set obj to obj & ",\"evening\":" & tEvening
            set obj to obj & ",\"reminder_date\":" & tReminder
            set obj to obj & ",\"deadline\":" & tDeadline
            set obj to obj & ",\"tags\":" & tTags
            set obj to obj & ",\"all_matching_tags\":" & tAllTags
            set obj to obj & ",\"status\":" & tStatus
            set obj to obj & ",\"completion_date\":" & tCompletion
            set obj to obj & ",\"is_logged\":" & isLoggedVal
            set obj to obj & ",\"notes\":" & tNotes
            set obj to obj & ",\"checklist\":null"
            set obj to obj & ",\"is_trashed\":false"
            set obj to obj & ",\"creation_date\":" & tCreation
            set obj to obj & ",\"modification_date\":" & tModification
            set obj to obj & "}"

            set end of jsonParts to obj
        end repeat
    end repeat

    set oldD to AppleScript's text item delimiters
    set AppleScript's text item delimiters to ","
    set jsonArr to "[" & (jsonParts as string) & "]"
    set AppleScript's text item delimiters to oldD
    return jsonArr
end tell
APPLESCRIPT;
    }
}
