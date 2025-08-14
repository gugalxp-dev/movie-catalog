<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\FavoriteMovieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('movies')->group(function () {
    Route::get('/search', [MovieController::class, 'search']);
    Route::get('/genres', [MovieController::class, 'genres']);
    Route::get('/now-playing-movies', [MovieController::class, 'nowPlayingMovies']);
    Route::get('/{id}', [MovieController::class, 'show']);
});

Route::prefix('favorites')->group(function () {
    Route::get('/', [FavoriteMovieController::class, 'index']);
    Route::post('/', [FavoriteMovieController::class, 'store']);
    Route::delete('/{tmdbId}', [FavoriteMovieController::class, 'destroy']);
    Route::get('/check/{tmdbId}', [FavoriteMovieController::class, 'check']);
});
