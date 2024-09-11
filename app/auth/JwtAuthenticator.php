<?php

namespace App\Auth;

use App\Services\LoggerService;
use App\Utils\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthenticator
{
    private $secret, $log;

    public function __construct()
    {
        $this->log = new LoggerService();
        $this->secret = Config::get('JWT_SECRET');
        $this->log->info("JWT inicializado con la secret {$this->secret}");
    }

    public function validateToken($token)
    {
        if (!$token) return false;

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return isset($decoded->user_id);
        } catch (\Firebase\JWT\SignatureInvalidException $se) {
            return false;
        }
    }

    // public function getDataFromToken($token)
    // {
    //     $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
    //     $this->log->info("Usuario autenticado: {$decoded->user_id} en sistema: {$decoded->system_id}");
    //     $data = (object)['user_id' => $decoded->user_id, 'system_id' => $decoded->system_id];

    //     return $data;
    // }
}
