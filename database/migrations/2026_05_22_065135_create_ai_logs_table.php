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
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            
            // Связи с другими таблицами
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('set null');
            $table->foreignId('message_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            
            // Информация о запросе к AI
            $table->string('provider')->default('openai'); // openai, anthropic, google, etc.
            $table->string('model')->nullable(); // gpt-4, claude-3, etc.
            $table->string('action')->nullable(); // chat, completion, embedding, etc.
            
            // Токены и стоимость
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            
            // Статусы
            $table->string('status')->default('success'); // success, error, timeout
            $table->text('error_message')->nullable();
            
            // Временные метки
            $table->float('response_time')->nullable(); // время ответа в секундах
            
            // Данные запроса и ответа в JSON
            $table->json('request_data')->nullable();   // Полные данные запроса
            $table->json('response_data')->nullable();  // Полные данные ответа
            $table->json('metadata')->nullable();       // Дополнительные метаданные
            
            $table->timestamps();
            
            // Индексы
            $table->index('user_id');
            $table->index('conversation_id');
            $table->index('message_id');
            $table->index('provider');
            $table->index('model');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
