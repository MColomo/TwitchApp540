<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\TwitchService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StreamControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetLiveStreamsEndpointReturnsStreamsSuccessfully()
    {
        // Simular una respuesta exitosa de la API de Twitch
        Http::fake([
            'https://api.twitch.tv/helix/streams' => Http::response([
                'data' => [
                    ['title' => 'Stream 1', 'user_name' => 'User1'],
                    ['title' => 'Stream 2', 'user_name' => 'User2']
                ]
            ], 200)
        ]);

        // Hacer una solicitud GET al endpoint
        $response = $this->get('/analytics/streams');

        // Verificar que la respuesta sea exitosa y contenga los datos correctos
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function testGetLiveStreamsEndpointHandlesUnauthorizedError()
    {
        // Simular un error de token inválido (401)
        Http::fake([
            'https://api.twitch.tv/helix/streams' => Http::response([], 401)
        ]);

        // Hacer una solicitud GET al endpoint
        $response = $this->get('/analytics/streams');

        // Verificar que se devuelva un error 401
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    public function testGetLiveStreamsEndpointHandlesInternalServerError()
    {
        // Simular un error del servidor (500)
        Http::fake([
            'https://api.twitch.tv/helix/streams' => Http::response([], 500)
        ]);

        // Hacer una solicitud GET al endpoint
        $response = $this->get('/analytics/streams');

        // Verificar que se devuelva un error 500
        $response->assertStatus(500);
        $response->assertJson(['error' => 'Internal server error.']);
    }
}
