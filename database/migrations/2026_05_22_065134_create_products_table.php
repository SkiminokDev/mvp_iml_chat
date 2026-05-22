<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Основная информация
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Цена и валюта
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            
            // Статусы
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // Категории и теги
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            
            // Изображения и медиа
            $table->string('image_url')->nullable();
            $table->json('images')->nullable();
            
            // Дополнительные данные в JSON
            $table->json('attributes')->nullable(); // Дополнительные атрибуты продукта
            $table->json('metadata')->nullable();   // Метаданные
            
            $table->timestamps();
            
            // Индексы
            $table->index('slug');
            $table->index('category');
            $table->index('is_active');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
