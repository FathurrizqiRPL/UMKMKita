<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedUmkm extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}