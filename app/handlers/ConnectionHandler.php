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
            $this->log->error('La conexión no pudo ser autenticada');
            $conn->close();
            return;
        }

        // Asociar la conexión con el usuario autenticado
        $data = $this->authenticator->getDataFromToken($token);
        $conn->user_id = $data->user_id;
        $conn->system_id = $data->system_id;
        $this->clients->attach($conn);

        $this->log->info("Usuario {$data->user_id} conectado en el sistema {$data->system_id}");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Lógica para manejar mensajes
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->log->info("Usuario {$conn->user_id} desconectado.");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->log->error("Error: {$e->getMessage()}");
        $conn->close();
    }

    private function extractTokenFromRequest(ConnectionInterface $conn)
    {
        // Implementación para extraer JWT del request
        try {
            $querystring = $conn->httpRequest->getUri()->getQuery();
            parse_str($querystring, $queryParams);

            $token = $queryParams['token'] ?? null;
            $this->log->info("Revisamos la información sobre el token: {$token} y ver si lo obtiene como debe");
            return $token;
        } catch (\Throwable $th) {
            $this->log->error($th->getMessage());
        }
    }
}
