<?php

namespace Database\Seeders;

use App\Models\AiLog;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AiLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $conversations = Conversation::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        if ($conversations->isEmpty()) {
            $this->command->warn('No conversations found. Please seed conversations first.');
            return;
        }

        // Создаем 100 логов AI
        AiLog::factory()->count(100)->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => fn() => $conversations->random()->id,
        ]);

        // Создаем успешные логи
        AiLog::factory()->count(50)->success()->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => fn() => $conversations->random()->id,
        ]);

        // Создаем логи с ошибками
        AiLog::factory()->count(20)->error()->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => fn() => $conversations->random()->id,
        ]);

        // Создаем логи OpenAI
        AiLog::factory()->count(30)->openai()->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => fn() => $conversations->random()->id,
        ]);

        // Создаем логи Anthropic
        AiLog::factory()->count(20)->anthropic()->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => fn() => $conversations->random()->id,
        ]);

        // Создаем логи с большим количеством токенов
        AiLog::factory()->count(10)->highTokens()->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => fn() => $conversations->random()->id,
        ]);
    }
}
