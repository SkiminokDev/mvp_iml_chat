<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('payment_settings', function (Blueprint $table) {
			$table->id();
			$table->string('payment_method')->nullable(); // 'card', 'yookassa', etc.
			$table->string('card_last_four', 4)->nullable();
			$table->boolean('auto_renew')->default(false);
			$table->decimal('balance', 10, 2)->default(0);
			$table->timestamps();

			$table->index('payment_method');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('payment_settings');
	}
};