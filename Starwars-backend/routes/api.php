<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarshipController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/starships/{id}/pilots', [StarshipController::class, 'addPilot']);
    Route::delete('/starships/{starshipId}/pilots/{pilotId}', [StarshipController::class, 'deletePilot']);
});

// Esta ruta aún está comentada, puedes activarla si la necesitas
// Route::get('/starships', [StarshipController::class, 'index']);
