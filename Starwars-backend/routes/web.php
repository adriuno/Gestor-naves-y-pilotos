<?php

use App\Http\Controllers\PilotController;
use App\Http\Controllers\StarshipController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/starships', [StarshipController::class, 'index']);
Route::get('/starships/{id}', [StarshipController::class, 'show']);


Route::delete('/pilots/{id}', [PilotController::class, 'destroy']);
Route::post('starships/{id}/pilots', [StarshipController::class, 'addPilot']);

Route::get('/starships/{id}/available-pilots', [StarshipController::class, 'availablePilots']);
Route::get('/pilots/{id}', [PilotController::class, 'show']);
