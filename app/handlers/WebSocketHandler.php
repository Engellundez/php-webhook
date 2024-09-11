<?php

namespace App\Handlers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Auth\JwtAuthenticator;  // Autenticador JWT
use App\Services\LoggerService;

class WebSocketHandler implements MessageComponentInterface
{
    protected $clients;
    protected $authenticator;
    protected $log;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->authenticator = new JwtAuthenticator(); // Inicializar el autenticador JWT
        $this->log = new LoggerService();
        $this->log->info('Entra al WebSocketHandler a hacer de las suyas y deja cargado el componente para su uso');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Autenticación del usuario
        $queryParams = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $params);

        if (!isset($params['token']) || !$this->authenticator->validateToken($params['token'])) {
            $this->log->info('Autenticación fallida.');
            $conn->send("Autenticación fallida.");
            $conn->close();
            return;
        }

        // Añadir el cliente autenticado a la lista de clientes
        $this->clients->attach($conn);
        $this->log->info("Nueva conexión: ({$conn->resourceId})");
        // echo "Nueva conexión: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->log->info("Conexión cerrada: ({$conn->resourceId})");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->log->error("Error: {$e->getMessage()}");
        $conn->close();
    }
}
