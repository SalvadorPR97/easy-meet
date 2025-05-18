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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Registrando usuario...');
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'birthdate' => 'required|date_format:Y-m-d|before:today|after:1900-01-01',
                'city' => 'required',
                'password' => 'required|string|min:8',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                Log::warning('Usuario no registrado. Error: Validación fallida');
                return response()->json(['message' => 'Campos incorrectos', 'errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'username' => $request->username,
                'email' => $request->email,
                'birthdate' => $request->birthdate,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            if ($request->hasFile('profile_pic')) {
                $path = $request->file('profile_pic')->store('public/profile_pics/' . $user->id);
                $user->profile_pic = Storage::url($path);
                $user->save();
            }

            Mail::to($request->email)->send(new VerifyEmail($request->email));
            Log::info('Usuario registrado correctamente: ' . $user->email);
            return response()->json(['data' => ['user' => $user]], 201);
        } catch (\Exception $e) {
            Log::error('Usuario no registrado. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function login(Request $request)
    {
        Log::info('Logueando usuario...');
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                Log::warning('Login fallido. Error: Validación fallida');
                return response()->json(['message' => 'Campos incorrectos'], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                Log::warning('Login fallido. Credenciales incorrectas para ' . $request->email);
                return response()->json(['message' => 'Credenciales incorrectas'], 422);
            }

            $user = User::where('email', $request['email'])->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Usuario logueado correctamente: ' . $user->email);
            return response()->json([
                'data' => [
                    'accessToken' => $token,
                    'toke_type' => 'Bearer',
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Login fallido. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function logout()
    {
        Log::info('Cerrando sesión de usuario: ' . auth()->user()->email);
        auth()->user()->tokens()->delete();
        Log::info('Sesión cerrada correctamente');
        return ['message' => 'Usuario deslogado'];
    }

    public function user()
    {
        Log::info('Obteniendo datos del usuario logueado: ' . auth()->user()->email);
        return response()->json(['data' => ['user' => auth()->user()]]);
    }

    public function regenerateCode(Request $request)
    {
        Log::info('Generando código de recuperación...');
        try {
            $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users']);
            if ($validator->fails()) {
                Log::warning('Error al generar código. Email inválido: ' . $request->email);
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

            Log::info('Código generado y enviado correctamente a: ' . $request->email);
            return ['message' => 'envío realizado', 'codigo' => $codigo];
        } catch (\Exception $e) {
            Log::error('Error al generar código de recuperación. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function regeneratePassword(Request $request)
    {
        Log::info('Regenerando contraseña...');
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
                'token' => 'required'
            ]);
            if ($validator->fails()) {
                Log::warning('Regeneración fallida. Validación incorrecta para: ' . $request->email);
                return response()->json(['message' => 'Campos incorrectos'], 422);
            }

            $updatePassword = DB::table('password_resets')
                ->where(['email' => $request->email, 'token' => $request->token])->first();

            if (!$updatePassword) {
                Log::warning('Regeneración fallida. Código inválido para: ' . $request->email);
                return response()->json(['message' => 'Código inválido'], 422);
            }

            $fechaActual = Carbon::now();
            $fechaCodMasUnMin = Carbon::parse($updatePassword->created_at)->addHours(5);
            if ($fechaActual->gt($fechaCodMasUnMin)) {
                Log::warning('Regeneración fallida. Código expirado para: ' . $request->email);
                return response()->json(['message' => 'Código expirado'], 422);
            }

            User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
            DB::table('password_resets')->where(['email' => $request->email])->delete();

            Log::info('Contraseña modificada correctamente para: ' . $request->email);
            return ['message' => 'Contraseña modificada correctamente'];
        } catch (\Exception $e) {
            Log::error('Error al regenerar contraseña. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function verifyEmail($email)
    {
        Log::info('Verificando email: ' . $email);
        try {
            $user = User::where('email', $email)->firstOrFail();
            $user->email_verified_at = Carbon::now();
            $user->verified = true;
            $user->save();

            Log::info('Email verificado correctamente para: ' . $email);
            return ['message' => 'Email verificado correctamente'];
        } catch (\Exception $e) {
            Log::error('Error al verificar email: ' . $email . '. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }
}
