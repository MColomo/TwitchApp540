<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwitchService
{
    protected $clientId;
    protected $clientSecret;
    protected $token;

    public function __construct()
    {
        $this->clientId = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
    }

    /**
     * 3. Verificación del Token de Twitch
     * - El método `getAccessToken()` se encarga de obtener y validar el token de acceso de Twitch.
     * - Si el token es inválido o ha expirado, la API devuelve un error que manejamos lanzando una excepción.
     * - La regeneración del token se hace automáticamente haciendo una nueva solicitud de token.
     */
    public function getAccessToken()
    {
        $response = Http::post(env('TWITCH_TOKEN_URL'), [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        // Manejar la respuesta en caso de éxito
        if ($response->successful()) {
            // Verificamos si existe la clave 'access_token'
            if (isset($response->json()['access_token'])) {
                $this->token = $response->json()['access_token'];
                return $this->token; // Token válido devuelto
            } 
        }
        
        // Si el token es inválido o ha expirado, se lanza un error
        $errorMessage = $response->json()['error'] ?? 'Error obtaining Twitch access token.';
        throw new \Exception($errorMessage);
    }

    /**
     * 2. Validación del ID
     * - Antes de realizar la consulta a la API de Twitch, el ID del usuario debe estar presente y ser un número válido.
     * - Esta validación se hará en el controlador `UserController`.
     */
    public function getUserInfo($userId)
    {
        $token = $this->getAccessToken(); // Obtener el token de acceso

        /**
         * 4. Consulta a la API de Twitch
         * - Se realiza una solicitud GET a la API de Twitch con el ID del usuario.
         * - Se incluyen los encabezados necesarios con el token de acceso y el ID del cliente.
         * - https://dev.twitch.tv/docs/api/get-started/#make-your-first-call -> Ejemplo de cabecera
         */
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Client-Id' => $this->clientId,
        ])->get(env('TWITCH_API_URL') . '/users', [
            'id' => $userId, // ID del usuario
        ]);

        // 4. Si el ID del usuario no existe en Twitch, se devuelve un error 404
        if ($response->status() === 404) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        elseif (!$response->successful()) {
            return response()->json(['error' => 'Internal server error.'], 500); // Si ocurre otro tipo de error, se devuelve un error 500
        }

        // 5. Respuesta al Cliente: Se devuelve la información del usuario si la consulta es exitosa
        return $response->json();
    }

    public function getLiveStreams()
    {
        // Se obtiene el token de acceso
        $token = $this->getAccessToken();

        // Se realiza una solicitud GET a la API de Twitch para obtener los streams en vivo
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Client-Id' => $this->clientId,
        ])->get(env('TWITCH_API_URL') . '/streams'); // Endpoint para obtener los streams

        // 4. Si el token es inválido o ha expirado, se devuelve un error 401
        if ($response->status() === 401) {
            return response()->json(['error' => 'Unauthorized. Twitch access token is invalid or has expired.'], 401);
        } elseif (!$response->successful()) {
            return response()->json(['error' => 'Internal server error.'], 500); // Si ocurre otro tipo de error, se devuelve un error 500
        }

        // 5. Respuesta al Cliente: Se devuelve la lista de streams en vivo en caso de éxito
        return $response->json();
    }
}
