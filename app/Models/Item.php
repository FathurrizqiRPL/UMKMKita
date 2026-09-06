<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
    'umkm_id',
    'type',
    'name',
    'description',
    'price',
    'duration',
    'image',
    'is_favorite',
    'is_best_seller',
];

protected $casts = [
    'is_favorite' => 'boolean',
    'is_best_seller' => 'boolean',
];
    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }
}
