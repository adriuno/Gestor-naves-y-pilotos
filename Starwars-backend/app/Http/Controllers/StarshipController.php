<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StarshipResource;
use App\Models\Pilot;
use App\Models\Starship;
use Illuminate\Http\Request;

class StarshipController extends Controller
{
    //
    public function index()
    {
        $starships = Starship::with('pilots')->paginate(36);
        return response()->json($starships);
    }


    public function show($id)
    {
        return response()->json(Starship::with('pilots')->findOrFail($id));
    }

    public function addPilot(Request $request, $starshipId)
    {
        $starship = Starship::with('pilots')->findOrFail($starshipId);
        $pilotId = $request->input('pilot_id');



        //ver si está vinculado
        $alreadyLinked = $starship->pilots()
            ->where('pilot_id', $pilotId)
            ->exists();


        if ($alreadyLinked) {
            return response()->json([
                'status' => 'error',
                'reason' => 'already_attached',
            ], 409); // podría usarse 409 Conflict también
        }

        $starship->pilots()->attach($pilotId);



        return response()->json([
            'status' => 'success',
            'reason' => 'pilot_attached',
        ], 201);
    }





    public function availablePilots($id)
    {
        $starship = Starship::with('pilots')->find($id);

        if (!$starship) {
            return response()->json([
                'status' => 'error',
                'reason' => 'starship_not_found',
            ], 404);
        }

        // Obtener IDs de pilotos ya vinculados
        $linkedPilotIds = $starship->pilots->pluck('id');

        // Buscar pilotos que NO estén en esa lista
        $availablePilots = Pilot::whereNotIn('id', $linkedPilotIds)->get();

        return response()->json([
            'status' => 'success',
            'reason' => 'available_pilots_loaded',
            'data' => $availablePilots,
        ]);
    }
}
