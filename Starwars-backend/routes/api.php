<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarshipController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SocialController;
use Illuminate\Support\Facades\Request;

Route::get('/pilots', [PilotController::class, 'index']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/starships/{id}/pilots', [StarshipController::class, 'addPilot']);
    Route::delete('/starships/{starshipId}/pilots/{pilotId}', [StarshipController::class, 'deletePilot']);
});
Route::get('/starships', [StarshipController::class, 'index']);
Route::middleware('auth:sanctum')->get('/starships/{id}', [StarshipController::class, 'show']);


// Reset contraseña
Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::post('register',[LoginController::class,'register']);
Route::post('login',[LoginController::class,'login']);
Route::post('logout',[LoginController::class,'logout'])
  ->middleware('auth:sanctum');

Route::get('/auth/google/redirect', [SocialController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialController::class, 'handleGoogleCallback']);



// Esta ruta aún está comentada, puedes activarla si se necesita
// Route::get('/starships', [StarshipController::class, 'index']);


