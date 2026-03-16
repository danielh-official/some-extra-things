<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
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
        ], array_merge($validated, ['is_trashed' => false]));

        // Check if this was an update
        $create = $item->wasRecentlyCreated;

        $this->syncTags($item, $validated);

        return (new ItemResource($item))
            ->response()
            ->setStatusCode($create ? 201 : 200);
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

    public function index(): JsonResponse
    {
        $items = Item::all();

        return (new ItemCollection($items))->response();
    }
}
