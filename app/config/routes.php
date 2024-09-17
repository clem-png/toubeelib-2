<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\GetRdvsByIdAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', HomeAction::class);

    $app->get('/rdvs/{ID-RDV}[/]', GetRdvsByIdAction::class)->setName('rdvsId');

    return $app;
};