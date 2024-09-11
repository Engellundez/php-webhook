<?php

namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Server implements MessageComponentInterface
{
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage; // Almacena las conexiones
    }

    public function onOpen(ConnectionInterface $conn) {
        // Conexión abierta
        $this->clients->attach($conn);
        echo "Nueva conexión ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Mensaje recibido
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Conexión cerrada
        $this->clients->detach($conn);
        echo "Conexión ({$conn->resourceId}) cerrada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Error en la conexión
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}
