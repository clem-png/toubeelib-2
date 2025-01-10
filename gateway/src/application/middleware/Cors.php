<?php

namespace gateway\application\middleware;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;

class Cors{

    public function __invoke(ServerRequestInterface $rq, RequestHandlerInterface $next ): ResponseInterface {
        if (! $rq->hasHeader('Origin'))
            New HttpUnauthorizedException ($rq, "missing Origin Header (cors)");
        $response = $next->handle($rq);
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:7080')
            ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, PATCH' )
            ->withHeader('Access-Control-Allow-Headers','Authorization' )
            ->withHeader('Access-Control-Max-Age', 3600)
        ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}