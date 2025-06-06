<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pilot;
use App\Models\Starship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

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
        // Sacamos el token de la cabecera Authorization
        $tokenHeader = $request->bearerToken();

        if (!$tokenHeader) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        // Buscamos el token en la base de datos
        $accessToken = PersonalAccessToken::findToken($tokenHeader);

        if (!$accessToken) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        // Obtenemos al usuario desde el token
        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 401);
        }

        // Ahora sí podemos acceder a sus starships
        $buscar = $request->query('buscar', '');
        $orden = 'name';

        $starships = $user->starships()
            ->with('pilots')
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


    // public function show($id)
    // {
    //     return response()->json(Starship::with('pilots')->findOrFail($id));
    // }


    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $starship = Starship::with('pilots')->where('user_id', $user->id)->findOrFail($id);
        return response()->json($starship);
    }












    // ------------ Funciones para BORRAR pilotos --------------

    // public function delete(Request $request, $starshipId, $pilotId){

    // }


    public function deletePilot($starshipId, $pilotId)
    {
        // $user = Auth::user();
        // // solo el admin puede borrar
        // if (!$user || !$user->admin) {
        //     return response()->json(['message' => 'No autorizado'], 403);
        // }

        $starship = Starship::findOrFail($starshipId);

        if (!$starship->pilots()->where('pilot_id', $pilotId)->exists()) {
            return response()->json([
                'status' => 'error',
                'reason' => 'not_attached',
            ], 404);
        }

        $starship->pilots()->detach($pilotId);

        return response()->json([
            'status' => 'success',
            'reason' => 'pilot_detached',
        ]);
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
