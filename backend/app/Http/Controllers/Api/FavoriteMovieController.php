<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FavoriteMovieService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class FavoriteMovieController extends Controller
{
    private $favoriteMovieService;

    public function __construct(FavoriteMovieService $favoriteMovieService)
    {
        $this->favoriteMovieService = $favoriteMovieService;
    }

    /**
     * List favorite movies
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'genre_ids' => 'nullable|array',
                'genre_ids.*' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid input data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $genreIds = $request->input('genre_ids');
            $favorites = $this->favoriteMovieService->getFavorites($genreIds);

            return response()->json([
                'success' => true,
                'data' => $favorites
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Internal server error while listing favorite movies'
            ], 500);
        }
    }

    /**
     * Add a movie to favorites
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|min:1',
                'title' => 'required|string|max:255',
                'overview' => 'nullable|string',
                'poster_path' => 'nullable|string|max:255',
                'backdrop_path' => 'nullable|string|max:255',
                'release_date' => 'nullable|date',
                'vote_average' => 'nullable|numeric|min:0|max:10',
                'vote_count' => 'nullable|integer|min:0',
                'genre_ids' => 'nullable|array',
                'genre_ids.*' => 'integer|min:1',
                'original_language' => 'nullable|string|max:10',
                'adult' => 'nullable|boolean',
                'popularity' => 'nullable|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid input data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $movieData = $request->all();
            $favoriteMovie = $this->favoriteMovieService->addToFavorites($movieData);

            return response()->json([
                'success' => true,
                'message' => 'Movie successfully added to favorites',
                'data' => $favoriteMovie
            ], 201);
        } catch (Exception $e) {
            Log::error('Error adding movie to favorites', [
                'message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            $statusCode = str_contains($e->getMessage(), 'already in your list') ? 409 : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Remove a movie from favorites
     */
    public function destroy(int $tmdbId): JsonResponse
    {
        try {
            $validator = Validator::make(['tmdb_id' => $tmdbId], [
                'tmdb_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid movie ID'
                ], 422);
            }

            $this->favoriteMovieService->removeFromFavorites($tmdbId);

            return response()->json([
                'success' => true,
                'message' => 'Movie successfully removed from favorites'
            ]);
        } catch (Exception $e) {
            Log::error('Error removing movie from favorites', [
                'message' => $e->getMessage(),
                'tmdb_id' => $tmdbId
            ]);

            $statusCode = str_contains($e->getMessage(), 'not found') ? 404 : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Check if a movie is in favorites
     */
    public function check(int $tmdbId): JsonResponse
    {
        try {
            $validator = Validator::make(['tmdb_id' => $tmdbId], [
                'tmdb_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid movie ID'
                ], 422);
            }

            $isFavorite = $this->favoriteMovieService->isFavorite($tmdbId);

            return response()->json([
                'success' => true,
                'data' => [
                    'is_favorite' => $isFavorite
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error checking if movie is favorite', [
                'message' => $e->getMessage(),
                'tmdb_id' => $tmdbId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error while checking favorite'
            ], 500);
        }
    }
}
