<?php

namespace App\Services;

use App\Models\FavoriteMovie;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class FavoriteMovieService
{
    /**
     * Add a movie to favorites
     */
    public function addToFavorites(array $movieData): FavoriteMovie
    {
        try {
            $existingMovie = FavoriteMovie::where('tmdb_id', $movieData['id'])->first();

            if ($existingMovie) {
                if (!$existingMovie->is_active) {
                    $existingMovie->update(['is_active' => true]);
                }
                return $existingMovie;
            }

            return FavoriteMovie::create([
                'tmdb_id' => $movieData['id'],
                'title' => $movieData['title'],
                'overview' => $movieData['overview'] ?? null,
                'poster_path' => $movieData['poster_path'] ?? null,
                'backdrop_path' => $movieData['backdrop_path'] ?? null,
                'release_date' => $movieData['release_date'] ?? null,
                'vote_average' => $movieData['vote_average'] ?? null,
                'vote_count' => $movieData['vote_count'] ?? null,
                'genre_ids' => $movieData['genre_ids'] ?? [],
                'original_language' => $movieData['original_language'] ?? null,
                'adult' => $movieData['adult'] ?? false,
                'popularity' => $movieData['popularity'] ?? null,
                'is_active' => true
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * List favorite movies
     */
    public function getFavorites(?array $genreIds = null): Collection
    {
        try {
            $query = FavoriteMovie::where('is_active', true);

            if ($genreIds && !empty($genreIds)) {
                $query->where(function ($q) use ($genreIds) {
                    foreach ($genreIds as $genreId) {
                        $q->orWhereJsonContains('genre_ids', $genreId);
                    }
                });
            }

            return $query->orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove a movie from favorites
     */
    public function removeFromFavorites(int $tmdbId): bool
    {
        try {
            $favoriteMovie = FavoriteMovie::where('tmdb_id', $tmdbId)
                ->where('is_active', true)
                ->first();

            if (!$favoriteMovie) {
                throw new Exception('Movie not found in favorites list');
            }

            return $favoriteMovie->update(['is_active' => false]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if a movie is in favorites
     */
    public function isFavorite(int $tmdbId): bool
    {
        try {
            return FavoriteMovie::where('tmdb_id', $tmdbId)->exists();
        } catch (Exception $e) {
            return false;
        }
    }
}
