<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    /**
     * The primary key type is a string (Things UUID).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The primary key is not auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'type',
        'title',
        'parent',
        'parent_id',
        'heading_id',
        'is_inbox',
        'start',
        'start_date',
        'evening',
        'reminder_date',
        'deadline',
        'tags',
        'all_matching_tags',
        'status',
        'completion_date',
        'is_logged',
        'is_trashed',
        'notes',
        'checklist',
        'creation_date',
        'modification_date',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_inbox' => 'bool',
            'evening' => 'bool',
            'is_logged' => 'bool',
            'is_trashed' => 'bool',
            'start_date' => 'date',
            'reminder_date' => 'datetime',
            'deadline' => 'date',
            'completion_date' => 'datetime',
            'tags' => 'array',
            'all_matching_tags' => 'array',
            'checklist' => 'array',
            'creation_date' => 'datetime',
            'modification_date' => 'datetime',
        ];
    }

    /**
     * Scope a query to exclude trashed items.
     */
    public function scopeNotTrashed(Builder $query): void
    {
        $query->where(fn (Builder $q) => $q->where('is_trashed', false)->orWhereNull('is_trashed'));
    }

    /**
     * The tags that belong to the item.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'item_tags');
    }
}
