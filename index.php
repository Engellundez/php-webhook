<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Server;
use App\Services\LoggerService;
use App\Utils\Config;

Config::load();

$key = Config::get('JWT_SECRET');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/auth') !== false) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            http_response_code(200);
            LoggerService::info("Autenticación exitosa");
        } catch (\Firebase\JWT\SignatureInvalidException $se) {
            http_response_code(400);
            LoggerService::error('No se proporcionó un token');
        }
    } else {
        http_response_code(400);
        LoggerService::error('No se proporcionó un token');
    }
    // exit;
}

// $server = new Server();
// $server->start();
