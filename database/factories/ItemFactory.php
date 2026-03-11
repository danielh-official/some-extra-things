<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => fake()->randomElement(['todo', 'heading', 'project', 'area']),
            'title' => fake()->sentence(3),
            'parent_id' => null,
            'heading_id' => null,
            'is_inbox' => false,
            'start' => null,
            'start_date' => null,
            'evening' => false,
            'reminder_at' => null,
            'deadline_at' => null,
            'tags' => [],
            'all_matching_tags' => [],
            'status' => 'open',
            'completed_at' => null,
            'is_logged' => false,
            'notes' => null,
            'checklist' => [],
        ];
    }
}
