<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{

public function isLikedByCurrentUser()
{
    if (!auth()->check()) {
        return false;
    }
    return $this->likes()->where('user_id', auth()->id())->exists();
}
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'category',
        'business_type',
        'description',
        'phone',
        'address',
        'landmark',
        'opening_time',
        'closing_time',
        'logo',
        'cover',
        'latitude',
        'longitude',
        'is_manual_closed',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_manual_closed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(UmkmLocation::class)
            ->orderBy('sort_order');
    }

    public function posters(): HasMany
    {
        return $this->hasMany(UmkmPoster::class)->orderBy('sort_order');
    }
}
