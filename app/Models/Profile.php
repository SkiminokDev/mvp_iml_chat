<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'middle_name',
		'phone',
		'birth_date',
		'avatar_path',
		'company_name',
		'bio',
	];

	protected $casts = [
		'birth_date' => 'date',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// Полное имя
	public function getFullNameAttribute(): string
	{
		return trim("{$this->last_name} {$this->first_name} {$this->middle_name}");
	}
}