<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Response;
use App\Http\Requests\Auth\VerifyTwoFactorRequest;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'direccion' => $request->direccion,
            'estado' => true,
            'id_rol' => $request->id_rol,
            'two_factor_secret' => encrypt($secret),
            'two_factor_confirmed' => false,
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente. Ahora escanee el QR o ingrese el código al Google Authenticator.',
            'user_id' => $usuario->id_usuario,
            'email' => $usuario->email,
            'manual_secret' => $secret,
            'qr_url' => url("/api/auth/users/{$usuario->id_usuario}/qr")
        ], 201);
    }

    public function showQr(int $id): Response|JsonResponse
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if (!$usuario->two_factor_secret) {
            return response()->json([
                'message' => 'El usuario no tiene secreto de doble factor'
            ], 422);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($usuario->two_factor_secret);

        $otpauthUrl = $google2fa->getQRCodeUrl(
            'TiendaComponentes',
            $usuario->email,
            $secret
        );

        $qr = QrCode::format('svg')
            ->size(300)
            ->generate($otpauthUrl);

        return response($qr, 200)->header('Content-Type', 'image/svg+xml');
    }


    public function verifyRegister2fa(VerifyTwoFactorRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(
            decrypt($usuario->two_factor_secret),
            $request->code
        );

        if (!$valid) {
            return response()->json(['message' => 'Código inválido'], 422);
        }

        $usuario->update([
            'two_factor_confirmed' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Google Authenticator activado correctamente',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        if (!$usuario->estado) {
            return response()->json(['message' => 'Usuario inactivo'], 403);
        }

        if (!$usuario->two_factor_confirmed) {
            return response()->json(['message' => 'Debe configurar Google Authenticator primero'], 403);
        }

        return response()->json([
            'message' => 'Credenciales válidas. Ingrese el código de Google Authenticator.',
            'two_factor_required' => true,
            'email' => $usuario->email,
        ]);
    }

    public function verifyLogin2fa(VerifyTwoFactorRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->with('rol')->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(
            decrypt($usuario->two_factor_secret),
            $request->code
        );

        if (!$valid) {
            return response()->json(['message' => 'Código inválido'], 422);
        }

        $usuario->tokens()->delete();

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in_minutes' => 60,
            'user' => $usuario,
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user()->load('rol'));
    }

    public function logout(): JsonResponse
    {
        $user = auth()->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }
}