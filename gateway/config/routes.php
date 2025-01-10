<?php
declare(strict_types=1);

use gateway\application\actions\GeneriquePraticienAction;
use gateway\application\middleware\Cors;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;


return function( App $app): App {

    $app->add(Cors::class);

    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    $app->get('/praticiens[/]', GeneriquePraticienAction::class)
        ->setName('praticiens');

    $app->get('/praticiens/{id}[/]', GeneriquePraticienAction::class)
        ->setName('praticiens-id');


    return $app;
};