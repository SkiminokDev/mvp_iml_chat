<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('profiles', function (Blueprint $table) {
			$table->id();

			// Связь с пользователем (один к одному)
			$table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

			// Личные данные
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('middle_name')->nullable();
			$table->string('phone', 20)->nullable();
			$table->date('birth_date')->nullable();

			// Дополнительно
			$table->text('avatar_path')->nullable();
			$table->string('company_name')->nullable();
			$table->text('bio')->nullable();

			$table->timestamps();

			// Индексы
			$table->index('user_id');
			$table->index('phone');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('profiles');
	}
};