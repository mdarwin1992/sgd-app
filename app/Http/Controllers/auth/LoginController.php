<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    /**
     * Tiempo de vida del token de acceso en segundos (ej. 5 minutos)
     * Debe coincidir con `ACCESS_TOKEN_LIFETIME_SECONDS` en el frontend.
     */
    protected $accessTokenLifetime = 3600 ; // 5 minutos

    /**
     * Tiempo de vida del refresh token en días (ej. 7 días)
     */
    protected $refreshTokenLifetimeDays = 7;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $rateLimiterKey = strtolower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            $secondsRemaining = RateLimiter::availableIn($rateLimiterKey);
            return response()->json([
                'message' => "Demasiados intentos. Por favor, inténtalo de nuevo en {$secondsRemaining} segundos."
            ], 429);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::clear($rateLimiterKey);
            $user = Auth::user();

            // --- Generar Access Token (para el frontend, corta duración) ---
            // Asegúrate de que tu modelo User use HasApiTokens trait
            $accessToken = $user->createToken('access_token', [], Carbon::now()->addSeconds($this->accessTokenLifetime))->plainTextToken;

            // --- Generar Refresh Token (para HttpOnly cookie, larga duración) ---
            // Crea un token con una capacidad específica, ej. 'refresh'
            // Este token no se envía directamente al frontend en el body JSON.
            // Se envía como una cookie HttpOnly.
            $refreshToken = $user->createToken('refresh_token', ['refresh'], Carbon::now()->addDays($this->refreshTokenLifetimeDays));

            // Establecer la cookie HttpOnly para el refresh token
            // El nombre 'refresh_token' debe ser consistente entre frontend y backend
            $cookie = cookie(
                'refresh_token', // Nombre de la cookie
                $refreshToken->plainTextToken, // Valor del token
                $this->refreshTokenLifetimeDays * 24 * 60, // Duración en minutos
                null, // Path
                null, // Domain
                true, // Secure (solo HTTPS)
                true, // HttpOnly (no accesible por JS)
                false, // SameSite (Laravel 8+ usa 'Lax' por defecto si no se especifica)
                'Lax' // SameSite attribute
            );

            // Obtener roles y permisos
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name'); // Spatie/Permission

            // Obtener oficina del usuario
            $office = Office::where('user_id', $user->id)->first(['id']);
            $officeId = $office ? $office->id : $user->id; // Considera cómo manejas user_office_id si no hay oficina

            // Obtener atributos del usuario
            $attributes = $this->getUserAttributes($user);

            // Retornar una respuesta exitosa con el token de acceso y datos del usuario
            return response()->json([
                'message' => 'Login exitoso',
                'access_token' => $accessToken,
                'expires_in' => $this->accessTokenLifetime, // Enviar tiempo de vida del access token
                'token_type' => 'Bearer',
                'user' => [ // Agrupa los datos del usuario en un objeto 'user'
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles,
                    'permissions' => $permissions,
                    'entity_id' => $user->entity_id,
                    'user_office_id' => $officeId,
                    'attributes' => $attributes,
                ]
            ])->withCookie($cookie); // Adjuntar la cookie del refresh token
        }

        RateLimiter::hit($rateLimiterKey);

        return response()->json([
            'message' => 'Las credenciales proporcionadas son incorrectas.'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario, incluyendo el de acceso actual
        // y el refresh token (si se ha almacenado como token de Sanctum).
        // Si usas Sanctum para el refresh token, esto los elimina.
        $request->user()->tokens()->delete();

        // Eliminar la cookie del refresh token del navegador
        $cookie = cookie()->forget('refresh_token');

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ])->withCookie($cookie); // Eliminar la cookie
    }

    public function profile(Request $request)
    {
        // Retornar los datos del perfil del usuario autenticado
        return response()->json($this->getUserAttributes($request->user()));
    }

    private function getUserAttributes($user)
    {
        return $user->only(['id', 'email', 'name', 'created_at']);
    }

    /**
     * Refresca el token de acceso utilizando el refresh token de la cookie HttpOnly.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        // Obtener el refresh token de la cookie
        $refreshTokenCookie = $request->cookie('refresh_token');

        if (!$refreshTokenCookie) {
            return response()->json(['error' => 'Refresh token no encontrado en la cookie.'], 401);
        }

        // Buscar el PersonalAccessToken asociado al refresh token
        // Necesitas que PersonalAccessToken sea importado y el trait HasApiTokens en tu modelo User.
        $refreshToken = \Laravel\Sanctum\PersonalAccessToken::findToken($refreshTokenCookie);

        // Validar el refresh token: que exista, que tenga la capacidad 'refresh' y que no haya expirado
        if (!$refreshToken || !$refreshToken->can('refresh') || $refreshToken->expires_at->isPast()) {
            // Si el refresh token no es válido o ha expirado, elimínalo
            if ($refreshToken) {
                $refreshToken->delete();
            }
            $cookie = cookie()->forget('refresh_token');
            return response()->json(['error' => 'Refresh token inválido o expirado. Inicia sesión de nuevo.'], 401)->withCookie($cookie);
        }

        $user = $refreshToken->tokenable;

        // Eliminar el refresh token antiguo para asegurar que solo se use una vez (seguridad)
        $refreshToken->delete();

        // Generar un nuevo access token de corta duración
        $newAccessToken = $user->createToken('access_token', [], Carbon::now()->addSeconds($this->accessTokenLifetime))->plainTextToken;

        // Generar un nuevo refresh token de larga duración y enviarlo como una nueva cookie HttpOnly
        $newRefreshToken = $user->createToken('refresh_token', ['refresh'], Carbon::now()->addDays($this->refreshTokenLifetimeDays));
        $newRefreshTokenCookie = cookie(
            'refresh_token',
            $newRefreshToken->plainTextToken,
            $this->refreshTokenLifetimeDays * 24 * 60,
            null,
            null,
            true, // Secure
            true, // HttpOnly
            false,
            'Lax' // SameSite
        );

        // Retornar el nuevo access token y los datos de usuario actualizados
        return response()->json([
            'access_token' => $newAccessToken,
            'expires_in' => $this->accessTokenLifetime,
            'token_type' => 'Bearer',
            // Opcional: Puedes devolver los datos de usuario actualizados aquí
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                // etc.
            ]
        ])->withCookie($newRefreshTokenCookie); // Adjuntar la nueva cookie del refresh token
    }
}
