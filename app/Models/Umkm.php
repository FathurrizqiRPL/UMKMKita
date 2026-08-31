<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'category',
        'description',
        'phone',
        'address',
        'landmark',
        'logo',
        'cover',
        'latitude',
        'longitude',
    ];

    protected $casts = [
    'latitude' => 'float',
    'longitude' => 'float',
    ];  

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
