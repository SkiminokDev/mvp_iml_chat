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
	        $table->text('text'); // Текст сообщения
	        $table->string('sender', 20)->default('user'); // 'user' или 'bot'
	        $table->text('response')->nullable(); // Ответ бота (если есть)
	        $table->ipAddress('ip_address')->nullable(); // IP пользователя
	        $table->timestamps(); // created_at и updated_at (автоматически)
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
