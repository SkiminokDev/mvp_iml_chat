<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'active_data',
        'balance',
    ];

    protected $casts = [
        'active' => 'boolean',
        'active_data' => 'datetime',
        'balance' => 'integer',
    ];

    /**
     * Связь с чатами (один ко многим)
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
