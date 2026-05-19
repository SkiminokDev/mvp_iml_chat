<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('chats', function (Blueprint $table) {
			$table->id();

			// Связь с пользователем (один ко многим)
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade');

			// Информация о чате
			$table->string('title')->default('Новый чат');
			$table->text('description')->nullable();

			// Статусы
			$table->boolean('is_active')->default(true);
			$table->boolean('is_archived')->default(false);

			// Метаданные
			$table->timestamp('last_message_at')->nullable();
			$table->integer('message_count')->default(0);

			$table->timestamps();

			// Индексы для быстрого поиска
			$table->index('user_id');
			$table->index('is_active');
			$table->index('created_at');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('chats');
	}
};