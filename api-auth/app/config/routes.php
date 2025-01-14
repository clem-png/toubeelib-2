<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use toubeelib_praticiens\application\actions\GetPraticiensAction;
use toubeelib_praticiens\application\actions\GetPraticiensByIdAction;
use toubeelib_praticiens\application\actions\HomeAction;
use toubeelib_praticiens\application\actions\PostPraticiensAction;


return function( App $app): App {

    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    $app->get('/', HomeAction::class);



    return $app;
};