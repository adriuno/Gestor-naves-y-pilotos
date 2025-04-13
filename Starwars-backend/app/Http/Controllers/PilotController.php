<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use Illuminate\Http\Request;

class PilotController extends Controller
{
    //
    public function destroy($id) {
        $pilot = Pilot::findOrFail($id);

        if (!$pilot) {
            return response()->json([
                'status' => 'error',
                'reason' => 'pilot_not_found',
            ], 404);
        }

        // $pilot->starships()->detach(); 
        // Borra las relaciones en la tabla pivote pero creo que no hace falta por tener bien lo de CASCASDEonDelete..

        $pilot->delete();


        //  USAR _ para luego facilidad en frontend??  o daría igual con "1espacio" ?? 
        return response()->json([
            'status' => 'success',
            'reason' => 'pilot_deleted',
        ], 200);
    }



    public function show($id)
{
    $pilot = Pilot::findOrFail($id);

    if (!$pilot) {
        return response()->json([
            'status' => 'error',
            'reason' => 'pilot_not_found',
        ], 404);
    }

    
    return response()->json([
        'status' => 'success',
        'reason' => 'pilot_loaded',
        'data' => $pilot,
    ]);
}


}
