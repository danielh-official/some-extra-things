<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    /**
     * Store a newly created Thing or update an existing one (upsert).
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $item = Item::updateOrCreate([
            'id' => $validated['id'],
        ], $validated);

        // Check if this was an update
        $create = $item->wasRecentlyCreated;

        $this->syncTags($item, $validated);

        $data = [
            'id' => $item->id,
            'type' => $item->type,
            'title' => $item->title,
            'parent_id' => $item->parent_id,
            'heading_id' => $item->heading_id,
            'is_inbox' => $item->is_inbox,
            'start' => $item->start,
            'start_date' => $item->start_date?->toDateString(),
            'evening' => $item->evening,
            'reminder_date' => $item->reminder_at?->toIso8601String(),
            'deadline' => $item->deadline_at?->toDateString(),
            'tags' => $item->tags ?? [],
            'all_matching_tags' => $item->all_matching_tags ?? [],
            'status' => $item->status,
            'completion_date' => $item->completed_at?->toIso8601String(),
            'is_logged' => $item->is_logged,
            'notes' => $item->notes,
            'checklist' => $item->checklist ?? [],
            'creation_date' => $item->creation_date?->toIso8601String(),
            'modification_date' => $item->modification_date?->toIso8601String(),
        ];

        return response()->json(
            $data,
            $create ? 201 : 200
        );
    }

    /**
     * Sync tags for the given item based on the validated payload.
     *
     * @param  array<string, mixed>  $data
     */
    protected function syncTags(Item $item, array $data): void
    {
        $rawTags = [];

        if (array_key_exists('tags', $data) && is_array($data['tags'])) {
            $rawTags = array_merge($rawTags, $data['tags']);
        }

        if (array_key_exists('all_matching_tags', $data) && is_array($data['all_matching_tags'])) {
            $rawTags = array_merge($rawTags, $data['all_matching_tags']);
        }

        $names = array_values(array_unique(array_filter($rawTags, static function ($value) {
            return is_string($value) && $value !== '';
        })));

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
