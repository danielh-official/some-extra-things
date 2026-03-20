<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ImportThings extends Controller
{
    public function __invoke(): RedirectResponse
    {
        Artisan::call('things:export');

        $json = Storage::get('things.json');

        if (! $json) {
            return redirect()->back()->with('import_error', 'Failed to read things.json after export.');
        }

        $items = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('import_error', 'Failed to parse things.json: '.json_last_error_msg());
        }

        $seen = [];

        foreach ($items as $data) {
            if (isset($seen[$data['id']])) {
                continue;
            }

            $seen[$data['id']] = true;

            $item = Item::updateOrCreate(['id' => $data['id']], $data);

            $this->syncTags($item, $data);
        }

        $count = count($seen);

        return redirect()->back()->with('import_status', "Imported {$count} items from Things 3.");
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncTags(Item $item, array $data): void
    {
        $rawTags = [];

        if (isset($data['tags']) && is_array($data['tags'])) {
            $rawTags = array_merge($rawTags, $data['tags']);
        }

        if (isset($data['all_matching_tags']) && is_array($data['all_matching_tags'])) {
            $rawTags = array_merge($rawTags, $data['all_matching_tags']);
        }

        $names = array_values(array_unique(array_filter($rawTags, static fn ($v) => is_string($v) && $v !== '')));

        if ($names === []) {
            $item->tags()->detach();

            return;
        }

        $tagIds = [];

        foreach ($names as $name) {
            $tag = Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        $item->tags()->sync($tagIds);
    }
}
