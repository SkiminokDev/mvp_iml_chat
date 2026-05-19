<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('llm_settings', function (Blueprint $table) {
			$table->id();
			$table->string('provider')->default('sber'); // 'sber', 'yandex', 'openai'
			$table->string('api_key')->nullable();
			$table->string('model_name')->default('default');
			$table->integer('max_tokens')->default(1000);
			$table->decimal('temperature', 3, 2)->default(0.7);
			$table->boolean('is_active')->default(true);
			$table->timestamps();

			$table->index('provider');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('llm_settings');
	}
};