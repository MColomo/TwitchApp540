<?php

namespace App\Http\Controllers;

use App\Services\TwitchService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $twitchService;

    public function __construct(TwitchService $twitchService)
    {
        $this->twitchService = $twitchService;
    }

    public function getUserInfo(Request $request)
    {
        // 2. Validación del ID: Aquí se valida que el ID esté presente y sea un número entero.
        // - Si no se cumple esta validación, se lanza automáticamente un error 400.
        $request->validate([
            'id' => 'required|integer',
        ]);

        try {
            // 4. Consulta a la API de Twitch: Se llama al método getUserInfo del TwitchService.
            $userInfo = $this->twitchService->getUserInfo($request->id);
            // 5. Respuesta al Cliente: Se devuelve la información del usuario con un código 200.
            return response()->json($userInfo, 200);
        } catch (\Exception $e) {
            // 5. Manejo de errores: Si ocurre un error inesperado, se devuelve un error 500.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
