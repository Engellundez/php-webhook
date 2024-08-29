<?php

require_once 'vendor/autoload.php';
require_once 'interface/Chat.php';
require_once 'interface/logger.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

error_reporting(~E_DEPRECATED);

Log::info("Servidor Inicializado");

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8010
);

$server->run();

