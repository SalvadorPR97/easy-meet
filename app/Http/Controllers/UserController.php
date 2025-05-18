<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Update the password of the current user.
     */
    public function updatePassword(Request $request)
    {
        Log::info("Actualizando contraseña del usuario ID: " . auth()->id());

        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
                'newPassword' => 'required|string|confirmed',
                'newPassword_confirmation' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning("Error en la validación al actualizar contraseña del usuario ID: " . auth()->id());
                return response()->json(['message' => 'Las contraseñas no coinciden'], 422);
            }

            $user = auth()->user();

            if (Hash::check($request->password, $user->password)) {
                $user->password = Hash::make($request->newPassword);
                $user->save();

                Log::info("Contraseña actualizada correctamente para el usuario ID: " . $user->id);
                return response()->json(['user' => $user]);
            }

            Log::warning("Contraseña actual incorrecta para el usuario ID: " . $user->id);
            return response()->json(['message' => 'Contraseña actual errónea'], 422);
        } catch (\Exception $e) {
            Log::error("Error al actualizar contraseña. Usuario ID: " . auth()->id() . ". Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    /**
     * Update the profile pic of the current user.
     */
    public function updateProfilePic(Request $request)
    {
        Log::info("Actualizando imagen de perfil del usuario ID: " . auth()->id());

        try {
            $validator = Validator::make($request->all(), [
                'profile_pic' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                Log::warning("Formato de imagen incorrecto para el usuario ID: " . auth()->id());
                return response()->json(['message' => 'Formato de imagen incorrecto'], 422);
            }

            $user = auth()->user();
            $path = $request->file('profile_pic')->store('public/profile_pics/' . $user->id);
            $user->profile_pic = Storage::url($path);
            $user->save();

            Log::info("Imagen de perfil actualizada correctamente para el usuario ID: " . $user->id);
            return response()->json(['message' => "Imagen actualizada"]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar imagen de perfil. Usuario ID: " . auth()->id() . ". Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }
}

