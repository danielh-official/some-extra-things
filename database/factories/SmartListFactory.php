<?php

namespace Database\Factories;

use App\Models\SmartList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SmartList>
 */
class SmartListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'criteria' => null,
        ];
    }
}
