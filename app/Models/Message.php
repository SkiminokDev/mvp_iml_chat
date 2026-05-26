<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Chat;
use App\Models\Conversation;
use App\Models\AiLog;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'type_place',
        'chat_id',
        'conversation_id',
        'text',
        'sender',
        'attachments',
        'metadata',
        'ai_data',
        'is_read',
        'is_deleted',
        'ip_address',
        'user_external_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_deleted' => 'boolean',
        'attachments' => 'array',
        'metadata' => 'array',
        'ai_data' => 'array',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Логи AI, связанные с сообщением
     */
    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiLog::class);
    }

    /**
     * _scopeUnread - фильтр непрочитанных сообщений
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * _scopeDeleted - фильтр удаленных сообщений
     */
    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }
}
