<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'release_date',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Songs::class);
    }

    // Get total songs
    public function getTotalSongsAttribute(): int
    {
        return $this->songs()->count();
    }
}