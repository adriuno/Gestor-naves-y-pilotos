<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StarshipResource;
use App\Models\Pilot;
use App\Models\Starship;
use Illuminate\Http\Request;

class StarshipController extends Controller
{

    public string $buscar = "";
    public string $orden = "name";


    //

    public function all()
    {
        return Starship::with('pilots')->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $buscar = $request->query('buscar', '');

        logger('Valor de buscar: ' . $buscar);

        $orden = 'name';

        $starships = Starship::with('pilots')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('name', 'like', "%{$buscar}%")
                        ->orWhereHas('pilots', function ($q2) use ($buscar) {
                            $q2->where('name', 'like', "%{$buscar}%");
                        });
                });
            })
            ->orderBy($orden)
            ->paginate(36);

        return response()->json($starships);
    }


    // public function index(Request $request)
    // {
    //     $buscar = $request->query('buscar', '');
    //     $orden = 'name'; // o lo que tengas definido

    //     $starships = Starship::with('pilots')
    //         ->when($buscar, function ($query) use ($buscar) {
    //             $query->where('name', 'like', "%{$buscar}%")
    //                   ->orWhereHas('pilots', function($q) use ($buscar) {
    //                       $q->where('name', 'like', "%{$buscar}%");
    //                   });
    //         })
    //         ->orderBy($orden)
    //         ->paginate(36);

    //     return response()->json($starships);
    // }




    //   FUNCIÓN en un inicio  !!!
    // public function index()
    // {
    //     // Obtenemos las naves que coincidan + función BSUCAR, para el nombre de la nave o el nombre del piloto
    //     $starships = Starship::with('pilots')
    //         ->where(function($q) {

    //             $q->where('name', 'like', "%{$this->buscar}%")
    //               ->orWhereHas('pilots', function($q2) {
    //                   $q2->where('name', 'like', "%{$this->buscar}%");
    //               });
    //         })
    //         ->orderBy($this->orden) 
    //         ->paginate(36);  

    //     return response()->json($starships);  
    // }


    public function show($id)
    {
        return response()->json(Starship::with('pilots')->findOrFail($id));
    }

    // ------------ Funciones para BORRAR pilotos --------------

    public function delete(Request $request, $starshipId, $pilotId){
        
    }

    // ------------- Funciones para AÑADIR pilotos -------------

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
