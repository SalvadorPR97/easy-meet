<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'newPassword' => 'confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Las contraseñas no coinciden'], 400);
        }
        $user = auth()->user();
        if (Hash::check($request->password, $user->password)) {
            $user->password = Hash::make($request->newPassword);
            $user->save();
            return response()->json(['user' => $user]);
        }
        return response()->json(['message' => 'Contraseña actual errónea'], 400);
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
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $path = $request->file('profile_picture')->store('public/profile_pictures');

        $user = auth()->user();
        $user->profile_pic = Storage::url($path);
        $user->save();
        return response()->json(['user' => $user]);
    }

    public function verifyEmail(Request $request){

    }
}
