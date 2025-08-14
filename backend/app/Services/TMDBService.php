<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class TMDBService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }

    /**
     * Search movies by name
     */
    public function searchMovies(string $query, int $page = 1)
    {
        try {
            $response = Http::get("{$this->baseUrl}/search/movie", [
                'api_key' => $this->apiKey,
                'query' => $query,
                'page' => $page,
                'language' => 'pt-BR' // Language for results
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get details of a specific movie
     */
    public function getMovieDetails(int $movieId)
    {
        try {
            $response = Http::get("{$this->baseUrl}/movie/{$movieId}", [
                'api_key' => $this->apiKey,
                'language' => 'pt-BR'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the list of genres
     */
    public function getGenres()
    {
        try {
            $response = Http::get("{$this->baseUrl}/genre/movie/list", [
                'api_key' => $this->apiKey,
                'language' => 'pt-BR'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['genres'] ?? [];
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get movies that are currently playing in theaters
     */
    public function getNowPlayingMovies(int $page = 1, string $region = 'BR')
    {
        try {
            $response = Http::get("{$this->baseUrl}/movie/now_playing", [
                'api_key' => $this->apiKey,
                'language' => 'pt-BR',
                'page' => $page,
                'region' => $region
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
