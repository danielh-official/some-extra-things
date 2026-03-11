<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'parent' => $this->parent,
            'parent_id' => $this->parent_id,
            'heading_id' => $this->heading_id,
            'is_inbox' => $this->is_inbox,
            'start' => $this->start,
            'start_date' => $this->start_date?->toDateString(),
            'evening' => $this->evening,
            'reminder_date' => $this->reminder_at?->toIso8601String(),
            'deadline' => $this->deadline_at?->toDateString(),
            'tags' => $this->tags ?? [],
            'all_matching_tags' => $this->all_matching_tags ?? [],
            'status' => $this->status,
            'completion_date' => $this->completed_at?->toIso8601String(),
            'is_logged' => $this->is_logged,
            'notes' => $this->notes,
            'checklist' => $this->checklist ?? [],
            'creation_date' => $this->creation_date?->toIso8601String(),
            'modification_date' => $this->modification_date?->toIso8601String(),
        ];
    }
}
