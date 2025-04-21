<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarshipController;

Route::post('/starships/{id}/pilots', [StarshipController::class, 'addPilot']);
Route::delete('/starships/{starshipId}/pilots/{pilotId}', [StarshipController::class, 'deletePilot']);

// Route::get('/starships', [StarshipController::class, 'index']);