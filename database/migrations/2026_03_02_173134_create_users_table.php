<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();

			// Авторизация
			$table->string('email')->unique();
			$table->string('password');
			$table->rememberToken(); // Для "запомнить меня"

			// Связи с настройками (внешние ключи)
			$table->foreignId('profile_id')->nullable()->constrained('profiles')->nullOnDelete();
			$table->foreignId('payment_settings_id')->nullable()->constrained('payment_settings')->nullOnDelete();
			$table->foreignId('llm_settings_id')->nullable()->constrained('llm_settings')->nullOnDelete();
			$table->foreignId('data_settings_id')->nullable()->constrained('data_settings')->nullOnDelete();

			// Статусы
			$table->boolean('is_active')->default(true);
			$table->boolean('is_admin')->default(false);
			$table->timestamp('email_verified_at')->nullable();

			$table->timestamps();

			// Индексы для быстрого поиска
			$table->index('email');
			$table->index('is_active');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users');
	}
};
