<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image',
        'release_date',
    ];

    public function songs()
    {
        return $this->hasMany(Songs::class);
    }
    // get total songs
    public function getTotalSongsAttribute()
    {
        return $this->songs()->count();
    }

    // 


    
}
