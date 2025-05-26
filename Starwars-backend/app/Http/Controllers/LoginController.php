<?php

namespace App\Http\Controllers;

use App\Models\Starship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function register(Request $request)
    {
        $registerUserData = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:60', 'not_regex:/[\p{So}\p{Cn}]/u'],
            'email' => ['required', 'string', 'max:60', 'email', 'unique:users'],
            'password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'not_regex:/\s/',
                'not_regex:/[\p{So}\p{Cn}]/u',
            ],
        ]);

        // Creamos el nuevo usuario
        User::create([
            'username' => $registerUserData['username'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);

        // Obtenemos las naves plantilla (sin user_id)
        
        /*
        $plantilla = Starship::whereNull('user_id')->with('pilots')->get();

        
        foreach ($plantilla as $naveOriginal) {
            $naveClonada = $user->starships()->create([
                'name' => $naveOriginal->name,
                'model' => $naveOriginal->model,
                'manufacturer' => $naveOriginal->manufacturer,
                'cost_in_credits' => $naveOriginal->cost_in_credits,
                'image2_url' => $naveOriginal->image2_url,
            ]);

            foreach ($naveOriginal->pilots as $pilot) {
                $naveClonada->pilots()->attach($pilot->id);
            }
        }
            */

        return response()->json([
            'message' => 'Usuario creado con éxito!',
        ]);
    }


    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $loginUserData['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email incorrecto!'
            ], 404);
        }
        if (!Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Contraseña incorrecta!'
            ], 401);
        }

        $token = $user->createToken('AuthToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user, // para ver datos del usuario luego en frontend!!

        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // requiere HasApiTokens en User

        return response()->json([
            "message" => "Salió con éxito"
        ]);
    }


    // public function login(Request $request){

    //     $loginUserData = $request->validate([
    //         'email' => 'required|string|email',
    //         'password' => 'required'
    //     ]);

    //     $user = User::where('email', $loginUserData['email'])->first();

    //     if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
    //         return response()->json([
    //             'message' => 'Correo o contraseña incorrectos'
    //         ], 401);
    //     }

    //     $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

    //     return response()->json([
    //         'access_token' => $token,
    //     ]);
    // }



}
