<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class MovieController extends Controller
{
    private $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Search Films
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255',
                'page' => 'nullable|integer|min:1|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Dados de entrada inválidos', 'errors' => $validator->errors()], 422);
            }

            $query = $request->input('query');
            $page = $request->input('page', 1);

            $movies = $this->tmdbService->searchMovies($query, $page);

            return response()->json(['success' => true, 'data' => $movies]);
        } catch (Exception $e) {
            Log::error('Erro na busca de filmes', [
                'message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json(['success' => false, 'message' => 'Erro interno do servidor ao buscar filmes'], 500);
        }
    }

    /**
     * Obter detalhes de um filme específico
     */
    public function show(int $id): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ID do filme inválido'], 422);
            }

            $movie = $this->tmdbService->getMovieDetails($id);

            return response()->json(['success' => true, 'data' => $movie]);
        } catch (Exception $e) {
            Log::error('Erro ao obter detalhes do filme', [
                'message' => $e->getMessage(),
                'movie_id' => $id
            ]);

            return response()->json(['success' => false, 'message' => 'Erro interno do servidor ao obter detalhes do filme'], 500);
        }
    }

    /**
     * Obter lista de gêneros
     */
    public function genres(): JsonResponse
    {
        try {
            $genres = $this->tmdbService->getGenres();

            return response()->json(['success' => true, 'data' => $genres]);
        } catch (Exception $e) {
            Log::error('Erro ao obter lista de gêneros', [
                'message' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Erro interno do servidor ao obter lista de gêneros'], 500);
        }
    }

    /**
     * Retorna filmes em cartaz
     */

    public function nowPlayingMovies(): JsonResponse
    {
        try {
            $response = $this->tmdbService->getNowPlayingMovies();

            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 500);
        }
    }
}
