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
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Первичный ключ (auto-increment)
            
            // Связи
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('chat_id')->nullable()->constrained('chats')->onDelete('set null');
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('set null');
            
            // Контент сообщения
            $table->text('text'); // Текст сообщения
            $table->string('sender', 20)->default('user'); // 'user' или 'bot'
            $table->json('attachments')->nullable(); // Вложения (файлы, изображения)
            
            // AI-данные
            $table->json('metadata')->nullable(); // Метаданные сообщения
            $table->json('ai_data')->nullable(); // Данные от AI (если сообщение от бота)
            
            // Статусы
            $table->boolean('is_read')->default(false);
            $table->boolean('is_deleted')->default(false);
            
            $table->ipAddress('ip_address')->nullable(); // IP пользователя
            $table->timestamps(); // created_at и updated_at (автоматически)
            
            // Индексы
            $table->index('user_id');
            $table->index('chat_id');
            $table->index('conversation_id');
            $table->index('sender');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
