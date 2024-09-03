<?php

namespace App\Auth;

use App\Services\LoggerService as Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthenticator
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function authenticate($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            Log::info("el token decodificado es: " . json_encode($decoded));
            return isset($decoded->userId);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUserIdFromToken($token)
    {
        $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
        Log::info("el token decodificado es: $decoded");
        return $decoded->userId ?? null;
    }
}
