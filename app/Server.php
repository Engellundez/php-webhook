<?php

namespace App;

use App\Auth\JwtAuthenticator;
use App\Utils\Config;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Handlers\ConnectionHandler;
use App\Handlers\MessageHandler;
use App\Handlers\RoomHandler;
use App\Services\LoggerService;

// Cargar la configuración
Config::load();

class Server
{
    private $log;

    public function __construct()
    {
        $this->log = new LoggerService();
    }

    public function start()
    {
        $port = Config::get('WEBSOCKET_PORT');
        $secretWord = Config::get('JWT_SECRET');

        $this->log->info("Servidor Inicializado en el puerto $port y con la secret $secretWord");
        $mySecret = new JwtAuthenticator($secretWord);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ConnectionHandler($mySecret) // Inicializa tu manejador de conexiones
                )
            ),
            $port
        );

        $server->run();
    }
}
