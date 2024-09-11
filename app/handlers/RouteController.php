<?php

namespace App\Handlers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use App\Services\LoggerService;

class RouteController
{
    public function __invoke(RequestInterface $request)
    {
        $path = $request->getUri()->getPath();

        switch ($path) {
            case '/control':
                return new Response(200, ['Content-Type' => 'text/html'], '<h1>Dashboard</h1>');
            case '/':
                return $this->controlView();
            case '/general':
                return $this->generalView();
            default:
                return new Response(404, ['Content-Type' => 'text/plain'], 'Ruta no encontrada');
        }
    }

    protected function controlView()
    {
        // Genera la vista de control
        return new Response(200, ['Content-Type' => 'text/html'], '<h1>Control de WebSockets</h1>');
    }

    protected function generalView()
    {
        // Genera la vista general
        return new Response(200, ['Content-Type' => 'text/html'], '<h1>Vista General de Grupos</h1>');
    }
}
