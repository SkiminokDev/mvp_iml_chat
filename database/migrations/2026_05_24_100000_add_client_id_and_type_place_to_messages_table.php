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
        Schema::table('messages', function (Blueprint $table) {
            $table->integer('client_id')->nullable()->after('user_id');
            $table->string('type_place')->nullable()->after('client_id');
            
            // Индексы для новых полей
            $table->index('client_id');
            $table->index('type_place');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['type_place']);
            $table->dropColumn(['client_id', 'type_place']);
        });
    }
};
