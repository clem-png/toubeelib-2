<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use toubeelib_praticiens\application\actions\GetPraticiensAction;
use toubeelib_praticiens\application\actions\GetPraticiensByIdAction;
use toubeelib_praticiens\application\actions\GetSpecialiteByIdAction;
use toubeelib_praticiens\application\actions\HomeAction;
use toubeelib_praticiens\application\actions\PostPraticiensAction;


return function( App $app): App {
    
    $app->get('/', HomeAction::class);

    //praticiens

    $app->get('/praticiens[/]', GetPraticiensAction::class)
        ->setName('praticiens');

    $app->get('/praticiens/{ID-PRATICIEN}[/]', GetPraticiensByIdAction::class)
        ->setName('praticiensId');

    $app->post('/praticiens[/]', PostPraticiensAction::class)
        ->setName('praticiensAdd');

    $app->get('/specialites/{ID-SPECIALITE}[/]', GetSpecialiteByIdAction::class)
        ->setName('specialiteId');

    return $app;
};