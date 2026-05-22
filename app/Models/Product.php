<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'is_active',
        'is_featured',
        'category',
        'tags',
        'image_url',
        'images',
        'attributes',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'images' => 'array',
        'attributes' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Разговоры, связанные с продуктом
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Логи AI, связанные с продуктом
     */
    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiLog::class);
    }

    /**
     * Пользователи, у которых есть разговоры об этом продукте
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, Conversation::class);
    }

    /**
     *_scopeActive - фильтр активных продуктов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     *_scopeFeatured - фильтр избранных продуктов
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Получить тег как строку (если массив)
     */
    public function getTagsAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }
}
