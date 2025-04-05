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
        //validaciones de campos que viajan en la request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'age' => 'required|string',
            'password' => 'required|string|min:8',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Campos incorrectos'], 422);
        }
        //en caso de cumplir las validaciones, se crea el nuevo usuario en bbdd
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'email' => $request->email,
            'age' => $request->age,
            'password' => Hash::make($request->password),
        ]);

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('public/profile_pics/' . $user->id);
            $user->profile_pic = Storage::url($path);
            $user->save();
        }

        //Envío de email de confirmación de la cuenta
        Mail::to($request->email)->send(new VerifyEmail($request->email));

        //se devuelve respuesta con los datos del nuevo usuario
        return response()->json(['data' => ['user' => $user]], 201);
    }

    public function login(Request $request)
    {
        //validaciones de campos que viajan en la request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Campos incorrectos'], 422);
        }
        //en caso de cumplir las validaciones, se comprueban las credenciales
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales incorrectas'], 422);
        }

        //en caso de credenciales correctas, se recupera la información del usuario
        $user = User::where('email', $request['email'])->firstOrFail();

        //se crea y almacena el token de autenticación
        $token = $user->createToken('auth_token')->plainTextToken;

        //se devuelve respuesta con los datos del usuario logado
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
        //validaciones de campos que viajan en la request
        $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users']);
        if ($validator->fails()) {
            return response()->json(['message' => 'Email incorrecto'], 404);
        }
        //en caso de cumplir las validaciones, se genera un código aleatorio
        $codigo = rand(000000, 999999);

        //se eliminan de la tabla password_resets de bbdd
        //todos los registros de códigos asociados al email que llega cómo entrada
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        //se inserta en la tabla password_resets de bbdd el código generado asociado al email,
        //también se le asigna un timestamp con el momento actual para gestionar la caducidad
        //del código generado.
        //"Carbon" es una librería de PHP para trabajar con fechas y horas de forma cómoda
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $codigo,
            'created_at' => Carbon::now()
        ]);

        //en un proceso real, tendriamos que añadir desarrollo aquí
        //para enviar por email el código generado, y que el usuario
        //pudiese continuar con el segundo paso del proceso
        // Enviar el código por correo
        Mail::to($request->email)->send(new PasswordResetCode($codigo));

        //se devuelve la salida con un mensaje informativo
        return ['message' => 'envío realizado', 'codigo' => $codigo];
    }

    public function regeneratePassword(Request $request)
    {
        //validaciones de campos que viajan en la request
        $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required']);
        if ($validator->fails()) {
            return response()->json(['message' => 'Campos incorrectos'], 422);
        }

        //en caso de cumplir las validaciones, se consulta en bbdd si el código (token)
        //es el que está asociado al email en la tabla password_resets
        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])->first();

        //si no se encuentra registro en la consulta anterior, se devuelve error
        if (!$updatePassword) {
            return response()->json(['message' => 'Código inválido'], 422);
        }

        //esta parte es para ver si el código ha expirado
        //en este caso se implementa para que expire en un minuto
        //en caso de haber expirado se devuelve error
        $fechaActual = Carbon::now();
        $fechaCodMasUnMin = Carbon::parse($updatePassword->created_at)->addHours(5);
        if ($fechaActual->gt($fechaCodMasUnMin)) {
            return response()->json(['message' => 'Código expirado'], 422);
        }

        //en caso de superar todas las validaciones, se actualiza la password hasheada en bbdd
        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        //se eliminan los registros de la tabla password_resets asociados al email de entrada
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        //se devuelve la salida con un mensaje informativo
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
