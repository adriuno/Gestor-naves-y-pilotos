<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarshipController;
use App\Http\Controllers\PilotController;

Route::get('/pilots', [PilotController::class, 'index']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/starships/{id}/pilots', [StarshipController::class, 'addPilot']);
    Route::delete('/starships/{starshipId}/pilots/{pilotId}', [StarshipController::class, 'deletePilot']);
});

Route::post('register',[LoginController::class,'register']);
Route::post('login',[LoginController::class,'login']);
Route::post('logout',[LoginController::class,'logout'])
  ->middleware('auth:sanctum');



// Esta ruta aún está comentada, puedes activarla si la necesitas
// Route::get('/starships', [StarshipController::class, 'index']);


