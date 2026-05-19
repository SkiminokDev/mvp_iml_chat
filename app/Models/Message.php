<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Chat;

class Message extends Model
{
	protected $fillable = [
		'chat_id',
		'text',
		'sender',
		'response',
		'ip_address',
	];

	public function chat(): BelongsTo
	{
		return $this->belongsTo(Chat::class);
	}

	public function user()
	{
		return $this->hasOneThrough(User::class, Chat::class);
	}
}
