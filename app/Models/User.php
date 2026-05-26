<?php

namespace App\Models;

// Добавьте этот импорт
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Chat;
use App\Models\Message;

class User extends Authenticatable
{
	// Добавьте HasApiTokens сюда
	use HasApiTokens, HasFactory, Notifiable;

	protected $fillable = [
		'email',
		'password',
		'profile_id',
		'payment_settings_id',
		'llm_settings_id',
		'data_settings_id',
		'is_active',
		'is_admin',
		'email_verified_at',
		'external_id',
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'password' => 'hashed',
		'is_active' => 'boolean',
		'is_admin' => 'boolean',
	];

	// Связи
	public function profile()
	{
		return $this->belongsTo(Profile::class);
	}

	public function paymentSettings()
	{
		return $this->belongsTo(PaymentSetting::class);
	}

	public function llmSettings()
	{
		return $this->belongsTo(LlmSetting::class);
	}

	public function dataSettings()
	{
		return $this->belongsTo(DataSetting::class);
	}

	public function messages()
	{
		return $this->hasManyThrough(Message::class, Chat::class);
	}

	public function chats()
	{
		return $this->hasMany(Chat::class);
	}

	public function lastMessage()
	{
		return $this->hasOne(Message::class)->latestOfMany();
	}
}