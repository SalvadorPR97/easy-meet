<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'birthdate' => 'required|date_format:Y-m-d|before:today|after:1900-01-01',
            'password' => 'required|string|min:8',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Campos incorrectos', 'errors' => $validator->errors()], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'password' => Hash::make($request->password),
        ]);

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('public/profile_pics/' . $user->id);
            $user->profile_pic = Storage::url($path);
            $user->save();
        }

        Mail::to($request->email)->send(new VerifyEmail($request->email));

        return response()->json(['data' => ['user' => $user]], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Campos incorrectos'], 422);
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales incorrectas'], 422);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => [
            'accessToken' => $token,
            'toke_type' => 'Bearer',
            'user' => $user]
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return ['message' => 'Usuario deslogado'];
    }

    public function user()
    {
        return response()->json(['data' => ['user' => auth()->user()]]);
    }

    public function regenerateCode(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users']);
        if ($validator->fails()) {
            return response()->json(['message' => 'Email incorrecto'], 404);
        }
        $codigo = rand(000000, 999999);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $codigo,
            'created_at' => Carbon::now()
        ]);

        Mail::to($request->email)->send(new PasswordResetCode($codigo));

        return ['message' => 'envío realizado', 'codigo' => $codigo];
    }

    public function regeneratePassword(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required']);
        if ($validator->fails()) {
            return response()->json(['message' => 'Campos incorrectos'], 422);
        }

        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])->first();

        if (!$updatePassword) {
            return response()->json(['message' => 'Código inválido'], 422);
        }

        $fechaActual = Carbon::now();
        $fechaCodMasUnMin = Carbon::parse($updatePassword->created_at)->addHours(5);
        if ($fechaActual->gt($fechaCodMasUnMin)) {
            return response()->json(['message' => 'Código expirado'], 422);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return ['message' => 'Contraseña modificada correctamente'];
    }

    public function verifyEmail($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        $user->email_verified_at = Carbon::now();
        $user->verified = true;
        $user->save();
        return ['message' => 'Email verificado correctamente'];
    }
}
