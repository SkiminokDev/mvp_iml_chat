<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use App\Models\Chat;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'chat_id' => null,
            'conversation_id' => null,
            'text' => fake()->paragraph(),
            'sender' => fake()->randomElement(['user', 'bot']),
            'attachments' => null,
            'metadata' => [
                'browser' => fake()->optional()->userAgent(),
                'platform' => fake()->randomElement(['web', 'ios', 'android']),
            ],
            'ai_data' => null,
            'is_read' => fake()->boolean(80),
            'is_deleted' => false,
            'ip_address' => fake()->ipv4(),
        ];
    }

    /**
     * Сообщение от пользователя.
     */
    public function fromUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender' => 'user',
        ]);
    }

    /**
     * Сообщение от бота.
     */
    public function fromBot(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender' => 'bot',
            'ai_data' => [
                'model' => fake()->randomElement(['gpt-4', 'claude-3', 'gemini']),
                'tokens_used' => fake()->numberBetween(50, 1000),
                'response_time' => fake()->randomFloat(2, 0.5, 5),
            ],
        ]);
    }

    /**
     * Непрочитанное сообщение.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Прочитанное сообщение.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Сообщение с вложениями.
     */
    public function withAttachments(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachments' => [
                [
                    'type' => 'image',
                    'url' => fake()->imageUrl(),
                    'name' => fake()->word() . '.jpg',
                    'size' => fake()->numberBetween(10000, 5000000),
                ],
            ],
        ]);
    }

    /**
     * Удаленное сообщение.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_deleted' => true,
        ]);
    }
}
