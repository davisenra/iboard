<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->words(3, true),
            'content' => $this->faker->paragraphs(3, true),
            'ip_address' => $this->faker->ipv4(),
        ];
    }

    public function thread(): static
    {
        return $this->state(function () {
            return [
                'file' => $this->faker->imageUrl(),
                'file_size' => $this->faker->randomNumber(5),
                'original_filename' => $this->faker->word().'.jpg',
                'file_resolution' => $this->faker->randomNumber(4).'x'.$this->faker->randomNumber(4),
                'last_replied_at' => $this->faker->dateTimeBetween('-1 day'),
                'post_id' => null,
            ];
        });
    }

    public function reply(): static
    {
        return $this->state(function () {
            return [
                'last_replied_at' => null,
            ];
        });
    }
}
