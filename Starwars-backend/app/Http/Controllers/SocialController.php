<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    // Paso 1: Redirigir al login de Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Paso 2: Google redirige aquí después del login
    public function handleGoogleCallback()
    {
        try {
            // Obtener los datos del usuario desde Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Solo para probar que llega bien

            // Buscar o crear el usuario en tu base de datos
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'username' => Str::kebab($googleUser->getName()), // este método pone el nombre con minúsculas, quita espacios!!
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            // Creamos igual aquí tbn un token para el usuario
            $token = $user->createToken('AuthToken')->plainTextToken;
            $frontendUrl = config('app.frontend_url') ?? 'http://localhost:3000';


            return redirect()->to($frontendUrl . '/login?access_token=' . $token . '&user=' . urlencode(json_encode([
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ])));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fallo en login con Google: ' . $e->getMessage()], 500);
        }
    }
}
