<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Mail\BienvenidoPaciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'sexo' => ['nullable', 'string', 'in:Femenino,Masculino,Prefiero no decir,F,M,Otro'],
            'semestre' => ['nullable', 'integer', 'min:1', 'max:8'],
            'numero_boleta' => ['required', 'string', 'digits:10', 'unique:pacientes,numero_boleta'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $data = $validator->validated();
        $sexo = $data['sexo'] ?? null;
        $sexoMap = [
            'Femenino' => 'F',
            'Masculino' => 'M',
            'Prefiero no decir' => 'Otro',
        ];
        if ($sexo !== null && array_key_exists($sexo, $sexoMap)) {
            $sexo = $sexoMap[$sexo];
        }
        if (!in_array($sexo, ['F', 'M', 'Otro'], true)) {
            $sexo = null;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'paciente',
        ]);

        $edad = Carbon::parse($data['fecha_nacimiento'])->age;

        $paciente = Paciente::create([
            'user_id' => $user->id,
            'sexo' => $sexo,
            'edad' => $edad,
            'semestre' => $data['semestre'] ?? null,
            'numero_boleta' => $data['numero_boleta'],
        ]);

        // correo al paciente
        Mail::to($user->email)->send(new BienvenidoPaciente($user));

        return response()->json([
            'message' => 'Te registraste correctamente. Revisa tu correo y confirma tu cuenta antes de iniciar sesión.',
            'requires_email_verification' => true,
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        // Revisar password
        if (!Auth::attempt($data)) {
            return response()->json([
                'errors' => ['Las credenciales son incorrectas']
            ], 422);
        }

        // Autenticar usuario
        $user = Auth::user();

        $shouldRequireEmailVerification = !app()->environment(['local', 'development', 'testing']);

        if (
            $shouldRequireEmailVerification
            && $user->role === 'paciente'
            && is_null($user->email_verified_at)
        ) {
            Auth::logout();

            return response()->json([
                'errors' => ['Debes verificar tu correo antes de acceder a la aplicación. Revisa tu bandeja de entrada.']
            ], 403);
        }

        return [
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user
        ];
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return [
            'user' => null
        ];
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'Si el correo está registrado en nuestro sistema, se ha enviado un enlace de recuperación a su correo electrónico con los pasos a seguir.']);
        }

        return response()->json(['errors' => ['email' => [__($status)]]], 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['status' => 'Contraseña restablecida exitosamente. Ahora puedes iniciar sesión con tu nueva contraseña.']);
        }

        return response()->json(['errors' => ['email' => [__($status)]]], 422);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->role !== 'paciente') {
            return response()->json([
                'errors' => ['No fue posible reenviar el correo de verificación para esta cuenta.']
            ], 422);
        }

        if (!is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'Tu correo ya está verificado. Ya puedes iniciar sesión.',
                'already_verified' => true,
            ]);
        }

        Mail::to($user->email)->send(new BienvenidoPaciente($user));

        return response()->json([
            'message' => 'Te reenviamos el correo de verificación. Revisa tu bandeja de entrada y spam.',
            'resent' => true,
        ]);
    }
}
