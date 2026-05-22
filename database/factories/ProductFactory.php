<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'currency' => fake()->randomElement(['USD', 'EUR', 'RUB', 'GBP']),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
            'category' => fake()->randomElement(['electronics', 'clothing', 'books', 'home', 'sports']),
            'tags' => fake()->randomElements(['new', 'sale', 'popular', 'limited', 'eco'], fake()->numberBetween(0, 3)),
            'image_url' => fake()->optional()->imageUrl(400, 300, 'product'),
            'images' => fake()->optional()->randomElements([
                fake()->imageUrl(400, 300, 'product'),
                fake()->imageUrl(400, 300, 'product'),
                fake()->imageUrl(400, 300, 'product'),
            ], fake()->numberBetween(1, 3)),
            'attributes' => [
                'weight' => fake()->optional()->numberBetween(100, 5000) . 'g',
                'dimensions' => fake()->optional()->randomElements([10, 20, 30, 40, 50]) . 'x' . 
                               fake()->randomElements([10, 20, 30, 40, 50]) . 'x' . 
                               fake()->randomElements([10, 20, 30, 40, 50]) . 'cm',
                'color' => fake()->optional()->safeColorName(),
            ],
            'metadata' => [
                'sku' => strtoupper(fake()->unique()->bothify('???-####')),
                'barcode' => fake()->optional()->ean13(),
                'stock' => fake()->numberBetween(0, 1000),
            ],
        ];
    }

    /**
     * Активный продукт.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Неактивный продукт.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Избранный продукт.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Продукт в категории electronics.
     */
    public function electronics(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'electronics',
        ]);
    }

    /**
     * Продукт со скидкой.
     */
    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'tags' => array_merge($attributes['tags'] ?? [], ['sale']),
            'price' => round($attributes['price'] * 0.7, 2),
        ]);
    }
}
