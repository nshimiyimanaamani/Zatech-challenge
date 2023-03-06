<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Songs extends Model
{


    use HasFactory;
    // define gernes
    const GERNES = [
        'pop',
        'rock',
        'jazz',
        'blues',
        'country',
        'hip-hop',
        'rap',
        'r&b',
        'soul',
        'reggae',
        'classical',
        'metal',
        'punk',
        'folk',
        'indie',
        'electronic',
        'dance',
        'disco',
        'funk',
        'ska',
        'techno',
        'trance',
        'world',
        'latin',
        'new-age',
        'gospel',
        'christian',
        'religious',
        'instrumental',
        'spoken-word',
        'audiobook',
        'comedy',
        'kids',
        'soundtrack',
        'musical',
        'opera',
        'holiday',
        'other'
    ];
    protected $fillable = [
        'title',
        'length',
        'image',
        'release_date',
        'gerne',
        'album_id'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
