<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    // Paso 1: Redirigir al login de Google
    public function redirectToGoogle()
    {
        return Socialite::with('google')->stateless()->redirect();
    }

    // Paso 2: Google redirige aquí después del login
    public function handleGoogleCallback()
    {
        try {
            // Obtener los datos del usuario desde Google
            $googleUser = Socialite::with('google')->stateless()->user();

            // Solo para probar que llega bien
            // Puedes comentar esta línea si todo funciona y quieres continuar
            // return response()->json($googleUser); // ✅ OPCIÓN 1: Ver JSON plano
             dd($googleUser); // ✅ OPCIÓN 2: Ver estructura de objeto

            // Buscar o crear el usuario en tu base de datos
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'username' => $googleUser->getName(),
                    'password' => bcrypt(uniqid()), // Generamos algo aleatorio (no se usará)
                ]
            );

            // Crear un token para el usuario
            $token = $user->createToken('authToken')->plainTextToken;

            // Redirigir al frontend con el token en la URL
            return redirect()->to("http://localhost:3000/social-login?token=$token");

        } catch (\Exception $e) {
            return response()->json(['error' => 'Fallo en login con Google: ' . $e->getMessage()], 500);
        }
    }
}
