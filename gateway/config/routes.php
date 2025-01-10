<?php
declare(strict_types=1);

use gateway\application\actions\GeneriquePraticienAction;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;


return function( App $app): App {

    $app->get('/praticiens[/]', GeneriquePraticienAction::class)
        ->setName('praticiens');

    return $app;
};