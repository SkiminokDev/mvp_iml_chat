<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Message;

class Chat extends Model
{
	protected $fillable = [
		'user_id',
		'title',
		'description',
		'is_active',
		'is_archived',
		'last_message_at',
		'message_count',
		'external_id',
	];

	protected $casts = [
		'is_active' => 'boolean',
		'is_archived' => 'boolean',
		'last_message_at' => 'datetime',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function messages() {
		return $this->hasMany(Message::class);
	}

	// Последнее сообщение
	public function lastMessage()
	{
		return $this->hasOne(Message::class)->latestOfMany();
	}

	public function hasPages()
	{
		return 10;
	}
}
