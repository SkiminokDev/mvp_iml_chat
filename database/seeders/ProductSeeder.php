<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем 20 продуктов
        Product::factory()->count(20)->create();

        // Создаем избранные продукты
        Product::factory()->count(5)->featured()->create();

        // Создаем продукты со скидкой
        Product::factory()->count(10)->onSale()->create();

        // Создаем конкретные продукты для тестирования
        Product::create([
            'name' => 'Премиум Подписка',
            'slug' => 'premium-subscription',
            'description' => 'Ежемесячная подписка на премиум функции',
            'price' => 999.00,
            'currency' => 'RUB',
            'is_active' => true,
            'is_featured' => true,
            'category' => 'subscriptions',
            'tags' => ['digital', 'subscription', 'popular'],
            'metadata' => [
                'sku' => 'SUB-PREM-001',
                'billing_cycle' => 'monthly',
                'features' => ['unlimited_messages', 'priority_support', 'advanced_analytics'],
            ],
        ]);

        Product::create([
            'name' => 'Консультация AI Эксперта',
            'slug' => 'ai-expert-consultation',
            'description' => 'Часовая консультация с AI экспертом',
            'price' => 5000.00,
            'currency' => 'RUB',
            'is_active' => true,
            'is_featured' => false,
            'category' => 'services',
            'tags' => ['service', 'consultation', 'expert'],
            'metadata' => [
                'sku' => 'SRV-AI-001',
                'duration' => '60 minutes',
                'format' => 'video_call',
            ],
        ]);
    }
}
