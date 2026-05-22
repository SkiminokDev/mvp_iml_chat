<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем тестового пользователя
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Создаем дополнительных пользователей
        User::factory()->count(10)->create();

        // Вызываем остальные сидеры в правильном порядке
        $this->call([
            ProductSeeder::class,
            ConversationSeeder::class,
            MessageSeeder::class,
            AiLogSeeder::class,
        ]);
    }
}
