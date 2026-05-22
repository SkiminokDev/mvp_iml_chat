<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use App\Models\Chat;
use App\Models\Conversation;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $conversations = Conversation::all();
        $chats = Chat::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // Создаем 200 сообщений
        Message::factory()->count(200)->create([
            'user_id' => fn() => $users->random()->id,
            'conversation_id' => $conversations->isNotEmpty() ? fn() => $conversations->random()->id : null,
            'chat_id' => $chats->isNotEmpty() ? fn() => $chats->random()->id : null,
        ]);

        // Сообщения от пользователя
        Message::factory()->count(50)->fromUser()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Сообщения от бота
        Message::factory()->count(50)->fromBot()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Непрочитанные сообщения
        Message::factory()->count(30)->unread()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Сообщения с вложениями
        Message::factory()->count(20)->withAttachments()->create([
            'user_id' => fn() => $users->random()->id,
        ]);
    }
}
