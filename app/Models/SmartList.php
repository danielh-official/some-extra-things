<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'criteria',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'criteria' => 'array',
        ];
    }

    /**
     * Build a base query for items matching this smart list's criteria.
     * When $invert is true, returns items that do NOT match the criteria.
     */
    public function itemsQuery(bool $invert = false): Builder
    {
        $criteria = $this->criteria;

        if ($invert && is_array($criteria) && $criteria !== []) {
            $matchingIds = (clone $this->itemsQuery())->pluck('id');

            return Item::notTrashed()
                ->where('status', 'Open')
                ->whereNotIn('id', $matchingIds);
        }

        $query = Item::notTrashed()
            ->where('status', 'Open');

        if (is_array($criteria) && $criteria !== []) {
            $this->applyCriteria($query, $criteria);
        }

        return $query;
    }

    /**
     * Get the count of items that match this smart list.
     */
    public function itemsCount(): int
    {
        return $this->itemsQuery()->count();
    }

    /**
     * Apply criteria tree to the given query.
     *
     * @param  array<string, mixed>  $node
     */
    protected function applyCriteria(Builder $query, array $node): void
    {
        if (! isset($node['type'])) {
            return;
        }

        if ($node['type'] === 'tag') {
            $this->applyTagCondition($query, $node);

            return;
        }

        if ($node['type'] === 'group') {
            $this->applyGroupCondition($query, $node);
        }
    }

    /**
     * Apply a single tag condition.
     *
     * @param  array<string, mixed>  $node
     */
    protected function applyTagCondition(Builder $query, array $node): void
    {
        if (! isset($node['tag'], $node['operator'])) {
            return;
        }

        $tagName = (string) $node['tag'];
        $operator = $node['operator'];

        if ($operator === 'equals') {
            $query->whereHas('tags', function (Builder $inner) use ($tagName): void {
                $inner->where('tags.name', $tagName);
            });

            return;
        }

        if ($operator === 'not_equals') {
            $query->whereDoesntHave('tags', function (Builder $inner) use ($tagName): void {
                $inner->where('tags.name', $tagName);
            });
        }
    }

    /**
     * Apply a group of conditions.
     *
     * @param  array<string, mixed>  $node
     */
    protected function applyGroupCondition(Builder $query, array $node): void
    {
        $logic = $node['logic'] ?? 'and';
        $conditions = $node['conditions'] ?? [];

        if (! is_array($conditions) || $conditions === []) {
            return;
        }

        $query->where(function (Builder $group) use ($conditions, $logic): void {
            foreach ($conditions as $index => $condition) {
                if (! is_array($condition)) {
                    continue;
                }

                $callback = function (Builder $nested) use ($condition): void {
                    $this->applyCriteria($nested, $condition);
                };

                if ($index === 0 || $logic === 'and') {
                    $group->where($callback);
                } else {
                    $group->orWhere($callback);
                }
            }
        });
    }
}
