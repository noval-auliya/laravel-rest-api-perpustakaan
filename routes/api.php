<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BukuController;
use App\Http\Controllers\API\KategoriBukuController;

Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);

    // kategori buku
    Route::apiResource('kategori', KategoriBukuController::class);

    // buku
    Route::apiResource('buku', BukuController::class);
});
