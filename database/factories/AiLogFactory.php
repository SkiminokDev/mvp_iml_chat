<?php

namespace Database\Factories;

use App\Models\AiLog;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiLog>
 */
class AiLogFactory extends Factory
{
    protected $model = AiLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $promptTokens = fake()->numberBetween(10, 5000);
        $completionTokens = fake()->numberBetween(10, 2000);
        
        return [
            'user_id' => User::factory(),
            'conversation_id' => Conversation::factory(),
            'message_id' => null,
            'product_id' => null,
            'provider' => fake()->randomElement(['openai', 'anthropic', 'google', 'azure', 'local']),
            'model' => fake()->randomElement([
                'gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo',
                'claude-3-opus', 'claude-3-sonnet', 'claude-3-haiku',
                'gemini-pro', 'gemini-ultra',
                'llama-2-70b', 'mistral-large'
            ]),
            'action' => fake()->randomElement(['chat', 'completion', 'embedding', 'image_generation', 'transcription']),
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'cost' => fake()->randomFloat(6, 0.0001, 0.5),
            'status' => fake()->randomElement(['success', 'success', 'success', 'error', 'timeout']),
            'error_message' => null,
            'response_time' => fake()->randomFloat(2, 0.1, 10),
            'request_data' => [
                'messages' => [
                    ['role' => 'user', 'content' => fake()->sentence()],
                ],
                'temperature' => fake()->randomFloat(1, 0, 1),
                'max_tokens' => fake()->numberBetween(100, 2000),
            ],
            'response_data' => [
                'choices' => [
                    [
                        'message' => ['role' => 'assistant', 'content' => fake()->paragraph()],
                        'finish_reason' => 'stop',
                    ]
                ],
                'usage' => [
                    'prompt_tokens' => $promptTokens,
                    'completion_tokens' => $completionTokens,
                    'total_tokens' => $promptTokens + $completionTokens,
                ],
            ],
            'metadata' => [
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'session_id' => fake()->uuid(),
            ],
        ];
    }

    /**
     * Успешный запрос.
     */
    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    /**
     * Запрос с ошибкой.
     */
    public function error(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'error',
            'error_message' => fake()->randomElement([
                'Rate limit exceeded',
                'Invalid API key',
                'Model not found',
                'Context window exceeded',
                'Network timeout',
            ]),
        ]);
    }

    /**
     * Запрос с таймаутом.
     */
    public function timeout(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'timeout',
            'error_message' => 'Request timed out after ' . fake()->numberBetween(30, 120) . 's',
            'response_time' => null,
        ]);
    }

    /**
     * Запрос к OpenAI.
     */
    public function openai(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'openai',
            'model' => fake()->randomElement(['gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo']),
        ]);
    }

    /**
     * Запрос к Anthropic.
     */
    public function anthropic(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'anthropic',
            'model' => fake()->randomElement(['claude-3-opus', 'claude-3-sonnet', 'claude-3-haiku']),
        ]);
    }

    /**
     * Запрос с большим количеством токенов.
     */
    public function highTokens(): static
    {
        return $this->state(fn (array $attributes) => [
            'prompt_tokens' => fake()->numberBetween(10000, 50000),
            'completion_tokens' => fake()->numberBetween(5000, 20000),
        ]);
    }
}
