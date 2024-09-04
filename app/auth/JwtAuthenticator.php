<?php

namespace App\Auth;

use App\Services\LoggerService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthenticator
{
    private $secret, $log;

    public function __construct($secret)
    {
        $this->log = new LoggerService();
        $this->log->info('Inicia con la Secret key, ' . $secret);
        $this->secret = $secret;
    }

    public function authenticate($token)
    {
        if (!$token) return false;

        try {
            $this->log->info('Iniciando decodificación del token: ' . $token);
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return isset($decoded->user_id);
        } catch (\Firebase\JWT\SignatureInvalidException $se) {
            return false;
        }
    }

    public function getDataFromToken($token)
    {
        $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
        $this->log->info("Usuario autenticado: {$decoded->user_id} en sistema: {$decoded->system_id}");
        $data = (object)['user_id' => $decoded->user_id, 'system_id' => $decoded->system_id];

        return $data;
    }
}
