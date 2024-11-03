Twitch Analytics API

Este proyecto es una aplicación en Laravel que proporciona endpoints para consultar información de streamers y streams en vivo desde la API de Twitch.
Requisitos Previos

    PHP >= 8.3.6
    Composer
    Laravel >= 11.9
    Cuenta de desarrollador en Twitch Developer Console

Instalación

Clona el repositorio:


    git clone https://github.com/MColomo/TwitchApp540.git
    cd twitch-analytics

Instala las dependencias:

    composer install

Configura las variables de entorno:

    Copia el archivo .env.example y renómbralo a .env.
    Configura tus credenciales de la API de Twitch en el archivo .env:

    TWITCH_CLIENT_ID=tu_client_id
    TWITCH_CLIENT_SECRET=tu_client_secret
    TWITCH_TOKEN_URL=https://id.twitch.tv/oauth2/token
    TWITCH_API_URL=https://api.twitch.tv/helix

Genera la clave de aplicación:

    php artisan key:generate

Ejecuta las migraciones (si es necesario):

    php artisan migrate

Inicia el servidor de desarrollo:

    php artisan serve

        La aplicación estará disponible en http://localhost:8000.

Endpoints
1. Consultar Información de un Streamer

    URL: /analytics/user
    Método: GET
    Parámetros de Consulta:
        id (requerido): El identificador del usuario de Twitch.
    Ejemplo de Solicitud:

    http

    GET /analytics/user?id=1234

    Posibles Respuestas:
        200 OK: Información del usuario.
        400 Bad Request: ID inválido o ausente.
        401 Unauthorized: Token de acceso inválido o expirado.
        404 Not Found: Usuario no encontrado.
        500 Internal Server Error: Error inesperado.

2. Consultar Streams en Vivo

    URL: /analytics/streams
    Método: GET
    Ejemplo de Solicitud:

    http

    GET /analytics/streams

    Posibles Respuestas:
        200 OK: Lista de streams en vivo.
        401 Unauthorized: Token de acceso inválido o expirado.
        500 Internal Server Error: Error inesperado.

Configuración de la API de Twitch

    Regístrate en Twitch Developer Console.
    Crea una nueva aplicación y obtén tu Client ID y Client Secret.
    Configura las URLs necesarias en tu archivo .env.

Pruebas (Generadas ChatGPT)

    php artisan test

    Las pruebas están ubicadas en tests/Unit/TwitchServiceTest.php y tests/Feature/StreamControllerTest.php.

Estructura del Proyecto

    app/Services/TwitchService.php: Servicio que se encarga de gestionar las solicitudes a la API de Twitch.
    app/Http/Controllers/UserController.php: Controlador para manejar las solicitudes de información de streamers.
    app/Http/Controllers/StreamController.php: Controlador para manejar las solicitudes de streams en vivo.
    routes/web.php: Definición de rutas de la aplicación.

Consideraciones de Seguridad

    Asegúrate de no exponer tus credenciales (Client ID y Client Secret) públicamente.
    Implementa controles adicionales para manejar la seguridad si planeas desplegar la aplicación en producción.

Recursos Adicionales

[Documentación de la API de Twitch](https://dev.twitch.tv/docs/api/)

[Laravel Documentation](https://laravel.com/docs/11.x)

NOTAS MCC:

Al no poder instalar dockers en mi equipo, he hecho el proyecto en WSL de Windows. 

La BBDD mySql estaba lanzada desde XAMPP en Windows.