<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TwitchService;
use Illuminate\Support\Facades\Http;

class TwitchServiceTest extends TestCase
{
    protected $twitchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twitchService = new TwitchService();
    }

    public function testGetAccessToken()
    {
        // Mock de la respuesta de la API de Twitch
        Http::fake([
            env('TWITCH_TOKEN_URL') => Http::sequence()
                ->push(['access_token' => 'fake_access_token', 'expires_in' => 3600, 'token_type' => 'bearer'])
                ->push(['error' => 'invalid_client', 'status' => 401]),
        ]);

        // Probar que obtenemos el token de acceso
        $token = $this->twitchService->getAccessToken();
        $this->assertEquals('fake_access_token', $token);

        // Probar que se maneja el error correctamente
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error obtaining Twitch access token.');
        $this->twitchService->getAccessToken(); // Llama nuevamente para simular el error
    }

    public function testGetUserInfoSuccess()
    {
        // Simular la respuesta de la API de Twitch para obtener el token
        Http::fake([
            env('TWITCH_TOKEN_URL') => Http::sequence()
                ->push(['access_token' => 'fake_access_token', 'token_type' => 'bearer']),

            // Simular la respuesta de la API de Twitch para obtener información del usuario
            env('TWITCH_API_URL') . '/users' => Http::sequence()
                ->push(['data' => [['id' => '1234', 'login' => 'test_user']]]), // Respuesta exitosa
        ]);

        // Probar que obtenemos la información del usuario correctamente
        $userInfo = $this->twitchService->getUserInfo('1234');

        $this->assertArrayHasKey('data', $userInfo);
        $this->assertIsArray($userInfo['data']); // Asegurarnos de que es un array
        $this->assertCount(1, $userInfo['data']); // Verificar que hay un usuario en el array
        $this->assertEquals('test_user', $userInfo['data'][0]['login']);
    }

    public function testGetUserInfoUserNotFound()
    {
        // Simular la respuesta de la API de Twitch para obtener el token
        Http::fake([
            env('TWITCH_TOKEN_URL') => Http::sequence()
                ->push(['access_token' => 'fake_access_token', 'token_type' => 'bearer']),

            // Simular la respuesta 404 de la API para un usuario no encontrado
            env('TWITCH_API_URL') . '/users' => Http::sequence()
                ->push(['error' => 'User not found'], 404),
        ]);

        // Probar que se maneja el error de usuario no encontrado correctamente
        $response = $this->twitchService->getUserInfo('invalid_id');
        $this->assertEquals(['error' => 'User not found.'], json_decode($response->getContent(), true));
    }

    public function testGetUserInfoInternalServerError()
    {
        // Simular la respuesta de la API de Twitch para obtener el token
        Http::fake([
            env('TWITCH_TOKEN_URL') => Http::sequence()
                ->push(['access_token' => 'fake_access_token', 'token_type' => 'bearer']),

            // Simular una respuesta de error interno del servidor
            env('TWITCH_API_URL') . '/users' => Http::sequence()
                ->push('', 500), // Respuesta 500
        ]);

        // Probar que se maneja el error interno del servidor correctamente
        $response = $this->twitchService->getUserInfo('123456');
        $this->assertEquals(['error' => 'Internal server error.'], json_decode($response->getContent(), true));
    }

}
