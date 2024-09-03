<?php

namespace App\Handlers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Auth\JwtAuthenticator;
use App\Services\LoggerService;

class ConnectionHandler implements MessageComponentInterface
{
    private $clients;
    private $authenticator;
    private $log;

    public function __construct(JwtAuthenticator $authenticator)
    {
        $this->clients = new \SplObjectStorage; // Para almacenar conexiones
        $this->authenticator = $authenticator;
        $this->log = new LoggerService();
        $this->log->info('ConnectionHandler activado');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $token = $this->extractTokenFromRequest($conn); // Obtener JWT del request

        if (!$this->authenticator->authenticate($token)) {
            $conn->close();
            return;
        }

        // Asociar la conexión con el usuario autenticado
        $userId = $this->authenticator->getUserIdFromToken($token);
        $conn->userId = $userId;
        $this->clients->attach($conn);

        $this->log->info("Usuario {$userId} conectado.");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Lógica para manejar mensajes
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->log->info("Usuario {$conn->userId} desconectado.");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->log->error("Error: {$e->getMessage()}");
        $conn->close();
    }

    private function extractTokenFromRequest(ConnectionInterface $conn)
    {
        // Implementación para extraer JWT del request
        $queryParams = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
        $userId = $queryParams['user_id'] ?? null;
    }
}
