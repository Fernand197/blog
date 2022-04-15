<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title(),
            'user_id' => $this->faker->randomElement(User::all()),
            'body' => $this->faker->text(200),
            'slug' => $this->faker->slug(),
            'image' => $this->faker->image(),
            'published' => $this->faker->boolean(),
        ];
    }
}
