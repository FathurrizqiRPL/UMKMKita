<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmPoster extends Model
{
    protected $fillable = [
        'umkm_id',
        'title',
        'image',
        'sort_order',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
