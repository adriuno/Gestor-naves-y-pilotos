<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ResetPasswordController extends Controller
{
    // Enviar enlace de restablecimiento de contraseña.

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        // genera token y envía el email ya que esa es función propia de laravel
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => trans($status)], 200);
        }

        if ($status === Password::INVALID_USER) {
            return response()->json(['error' => trans($status)], 404);
        }

        return response()->json(['error' => 'Prueba más tarde.'], 422);
    }


    // Restablecer la contraseña usando el token recibido.

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // laravel verifica aquí que el token es válido y que está asociado al email!! y ya actualiza la pass
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 422);
    }
}
