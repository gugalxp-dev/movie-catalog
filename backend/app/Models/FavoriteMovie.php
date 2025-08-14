<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteMovie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'title',
        'overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'vote_average',
        'vote_count',
        'genre_ids',
        'original_language',
        'adult',
        'popularity',
        'is_active'
    ];

    protected $casts = [
        'genre_ids' => 'array',
        'release_date' => 'date',
        'adult' => 'boolean',
        'vote_average' => 'decimal:1',
        'popularity' => 'decimal:3'
    ];
}
