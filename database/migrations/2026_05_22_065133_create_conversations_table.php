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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            
            // Связь с пользователем
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Связь с продуктом (опционально)
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            
            // Информация о разговоре
            $table->string('title')->default('Новый разговор');
            $table->text('description')->nullable();
            
            // Статусы
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->string('status')->default('active'); // active, closed, pending
            
            // Метаданные
            $table->timestamp('last_message_at')->nullable();
            $table->integer('message_count')->default(0);
            
            // Дополнительные данные в JSON
            $table->json('metadata')->nullable();
            $table->json('settings')->nullable();
            
            $table->timestamps();
            
            // Индексы
            $table->index('user_id');
            $table->index('product_id');
            $table->index('status');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
