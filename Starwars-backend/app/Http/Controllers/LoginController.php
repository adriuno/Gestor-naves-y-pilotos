<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    public function register(Request $request)
    {
        $registerUserData = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:60', 'unique:users,username', 'not_regex:/[\p{So}\p{Cn}\s]/u'],
            'email' => ['required', 'string', 'max:60', 'email:rfc', 'unique:users,email'],
            // se puede poner solo 'email' que valida que sea algo@prueba... sin el .com
            'password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'not_regex:/[\p{So}\p{Cn}\s]/u', // evitamos así emojis o espacios
            ],
        ]);

        // Creamos el nuevo usuario
        User::create([
            'username' => $registerUserData['username'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);

        // Sin el observer... pues habría que clonar plantillas cómo se hace debajo !!
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

        $key = $this->throttleKey($request);

        // Si ya está bloqueado, respondemos directamente
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ], 429);
        }

        // Registramos este intento AHORA
        RateLimiter::hit($key, 20); // Cuenta este intento desde ya

        $user = User::where('email', $loginUserData['email'])->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            // Ahora que ya contamos este intento, podemos consultar el total actual
            $remaining = max(0, 3 - RateLimiter::attempts($key));

            // Si ya no quedan intentos, forzamos el mensaje de bloqueo
            if ($remaining === 0) {
                return response()->json([
                    'message' => "Demasiados intentos. Intenta de nuevo en 20 segundos.",
                ], 429);
            }

            return response()->json([
                'message' => "Email o contraseña incorrectos. Te queda" . ($remaining === 1 ? '' : 'n') . " {$remaining} intento" . ($remaining === 1 ? '' : 's') . ".",
            ], 401);
        }

        // Éxito → limpiamos contador
        RateLimiter::clear($key);

        $token = $user->createToken('AuthToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // requiere HasApiTokens en User

    }



    // ------------- Métodos para el límite de intentos ---------------

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')); // servirá para bloquear ESE email si supera los intentos
        // Normaliza el email a minúsculas y lo usa como clave de intentos
    }
}
