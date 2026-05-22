<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'description',
        'is_active',
        'is_archived',
        'status',
        'last_message_at',
        'message_count',
        'metadata',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'last_message_at' => 'datetime',
        'message_count' => 'integer',
        'metadata' => 'array',
        'settings' => 'array',
    ];

    /**
     * Пользователь, которому принадлежит разговор
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Продукт, связанный с разговором (опционально)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Сообщения в разговоре
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Логи AI, связанные с разговором
     */
    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiLog::class);
    }

    /**
     * Последнее сообщение в разговоре
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     *_scopeActive - фильтр активных разговоров
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     *_scopeArchived - фильтр архивированных разговоров
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }
}
