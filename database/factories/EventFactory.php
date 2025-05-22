<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'location' => $this->faker->address,
            'city' => $this->faker->city,
            'date' => $this->faker->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'start_time' => $this->faker->time('H:i'),
            'min_participants' => 2,
            'max_participants' => $this->faker->numberBetween(3, 10),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'description' => $this->faker->paragraph,
            'owner_id' => User::factory(),
            'category_id' => Category::factory()->create()->id,
            'subcategory_id' => Subcategory::factory()->create()->id,
            'only_women' => 0,
            'only_men' => 0,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'image_url' => 'default.jpg',
        ];
    }
}
