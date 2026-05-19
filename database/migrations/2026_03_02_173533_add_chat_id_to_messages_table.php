<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('messages', function (Blueprint $table) {
			// Добавляем связь с чатом
			$table->foreignId('chat_id')->nullable()->after('id')->constrained('chats')->nullOnDelete();

			// Добавляем индексы
			$table->index('chat_id');
			$table->index('sender');
			$table->index('created_at');
		});
	}

	public function down(): void
	{
		Schema::table('messages', function (Blueprint $table) {
			$table->dropForeign(['chat_id']);
			$table->dropIndex(['chat_id']);
			$table->dropColumn('chat_id');
		});
	}
};