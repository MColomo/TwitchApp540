<?php

namespace App\Http\Controllers;

use App\Services\TwitchService;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    protected $twitchService;

    public function __construct(TwitchService $twitchService)
    {
        $this->twitchService = $twitchService;
    }

    /**
     * Método para manejar la solicitud GET y devolver los streams en vivo
     */
    public function getLiveStreams()
    {
        try {
            // 3. Consulta a la API de Twitch: Se llama al método getLiveStreams del TwitchService
            $liveStreams = $this->twitchService->getLiveStreams();
            
            // 5. Respuesta al Cliente: Se devuelve la lista de streams en vivo con un código 200
            return response()->json($liveStreams, 200);
        } catch (\Exception $e) {
            // 5. Manejo de errores: Si ocurre un error inesperado, se devuelve un error 500 con el mensaje de error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

