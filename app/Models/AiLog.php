<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiLog extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_id',
        'message_id',
        'product_id',
        'provider',
        'model',
        'action',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost',
        'status',
        'error_message',
        'response_time',
        'request_data',
        'response_data',
        'metadata',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'cost' => 'decimal:6',
        'response_time' => 'float',
        'request_data' => 'array',
        'response_data' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Пользователь, связанный с логом
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Разговор, связанный с логом
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Сообщение, связанное с логом
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Продукт, связанный с логом
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     *_scopeSuccess - фильтр успешных запросов
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     *_scopeError - фильтр ошибочных запросов
     */
    public function scopeError($query)
    {
        return $query->where('status', 'error');
    }

    /**
     *_scopeByProvider - фильтр по провайдеру
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Проверить, был ли запрос успешным
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Получить общую стоимость в читаемом формате
     */
    public function getFormattedCostAttribute(): string
    {
        return '$' . number_format($this->cost, 6);
    }
}
