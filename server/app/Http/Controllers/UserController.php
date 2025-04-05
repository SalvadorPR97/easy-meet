<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Update the password of the current user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'newPassword' => 'required|string|confirmed',
            'newPassword_confirmation' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Las contraseñas no coinciden'], 422);
        }
        $user = auth()->user();
        if (Hash::check($request->password, $user->password)) {
            $user->password = Hash::make($request->newPassword);
            $user->save();
            return response()->json(['user' => $user]);
        }
        return response()->json(['message' => 'Contraseña actual errónea'], 422);
    }

    /**
     * Update the profile pic of the current user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Formato de imagen incorrecto'], 422);
        }
        $user = auth()->user();

        $path = $request->file('profile_pic')->store('public/profile_pics/' . $user->id);
        $user->profile_pic = Storage::url($path);
        $user->save();
        return response()->json(['message' => "Imagen actualizada"]);
    }

}
