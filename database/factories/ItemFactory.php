<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'id' => (string) Str::uuid(),
            'type' => fake()->randomElement(['To-Do', 'Heading', 'Project', 'Area']),
            'title' => fake()->sentence(3),
            'parent_id' => null,
            'heading_id' => null,
            'is_inbox' => false,
            'start' => null,
            'start_date' => null,
            'evening' => false,
            'reminder_date' => null,
            'deadline' => null,
            'tags' => [],
            'all_matching_tags' => [],
            'status' => 'Open',
            'completion_date' => null,
            'is_logged' => false,
            'notes' => null,
            'checklist' => [],
        ];
    }
}
