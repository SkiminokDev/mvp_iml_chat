<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // Создаем 50 разговоров
        Conversation::factory()->count(50)->create([
            'user_id' => fn() => $users->random()->id,
            'product_id' => fn() => $products->isNotEmpty() ? $products->random()->id : null,
        ]);

        // Создаем активные разговоры
        Conversation::factory()->count(20)->active()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Создаем архивированные разговоры
        Conversation::factory()->count(10)->archived()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Создаем разговоры для каждого продукта (если есть продукты)
        if ($products->isNotEmpty()) {
            foreach ($products->take(5) as $product) {
                Conversation::factory()->count(3)->create([
                    'user_id' => fn() => $users->random()->id,
                    'product_id' => $product->id,
                    'title' => 'Вопрос о товаре: ' . $product->name,
                ]);
            }
        }
    }
}
