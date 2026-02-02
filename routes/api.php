<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::apiResource('books', BookController::class)->except(['store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('books', BookController::class)->only(['store']);
});

Route::apiResource('authors', AuthorController::class)
    ->only(['index', 'show']);

