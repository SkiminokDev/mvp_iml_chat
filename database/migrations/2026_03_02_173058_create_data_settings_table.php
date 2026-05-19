<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('data_settings', function (Blueprint $table) {
			$table->id();
			$table->boolean('save_history')->default(true);
			$table->boolean('allow_analytics')->default(false);
			$table->string('language', 5)->default('ru');
			$table->string('timezone', 50)->default('Europe/Moscow');
			$table->json('custom_settings')->nullable(); // Дополнительные настройки
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('data_settings');
	}
};
