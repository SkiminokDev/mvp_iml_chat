<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'is_active' => true,
            'is_archived' => false,
            'status' => fake()->randomElement(['active', 'closed', 'pending']),
            'last_message_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'message_count' => fake()->numberBetween(0, 100),
            'metadata' => [
                'source' => fake()->randomElement(['web', 'mobile', 'api']),
                'browser' => fake()->optional()->userAgent(),
            ],
            'settings' => [
                'notifications' => fake()->boolean(),
                'language' => fake()->randomElement(['en', 'ru', 'es', 'fr']),
            ],
        ];
    }

    /**
     * Индикатор того, что разговор активен.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'is_archived' => false,
            'status' => 'active',
        ]);
    }

    /**
     * Индикатор того, что разговор архивирован.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'is_archived' => true,
            'status' => 'closed',
        ]);
    }

    /**
     * Разговор с продуктом.
     */
    public function withProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => Product::factory(),
        ]);
    }

    /**
     * Разговор без продукта.
     */
    public function withoutProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => null,
        ]);
    }
}
