<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Handlers\WebSocketHandler;  // Manejador principal del WebSocket
use App\Services\LoggerService;
use App\Utils\Config;               // Utilidad para cargar configuraciones

use React\EventLoop\Loop;
use React\Http\HttpServer as HttpServerReact;
use React\Socket\SocketServer;
use App\Handlers\RouteController;

require __DIR__ . '/vendor/autoload.php';

// Cargar configuraciones
Config::load();
$ip = Config::get('SERVER_IP', 'localhost');
$port = Config::get('WS_PORT', 8080);

// Crear el loop de eventos de ReactPHP
$loop = Loop::get();

// Crear el servidor HTTP con ReactPHP
$httpServer = new HttpServerReact(new RouteController());
$socket = new SocketServer("$ip:$port", [], $loop);
$httpServer->listen($socket);

// Crear servidor WebSocket utilizando el loop
$webSocketServer = new HttpServer(new WsServer(new WebSocketHandler()));

// Crear IoServer y pasarlo al loop
$server = new IoServer($webSocketServer, $socket, $loop);

// Log de inicio del servidor
LoggerService::info("Servidor WebSocket escuchando en el puerto $port");

// Ejecutar el servidor
$loop->run();
