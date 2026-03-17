<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'things_id',
        'name',
        'keyboard_shortcut',
        'parent_tag_id',
        'parent_things_tag_id',
    ];

    public function parentTag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'parent_tag_id');
    }

    public function childTags(): HasMany
    {
        return $this->hasMany(Tag::class, 'parent_tag_id');
    }

    /**
     * Build the full ancestry path using > separator.
     */
    public function ancestryPath(): string
    {
        $parts = [];
        $current = $this;

        while ($current->parent_tag_id && ($parent = Tag::find($current->parent_tag_id))) {
            array_unshift($parts, $parent->name);
            $current = $parent;
        }

        return implode(' > ', $parts);
    }

    /**
     * The items that belong to the tag.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_tags');
    }
}
