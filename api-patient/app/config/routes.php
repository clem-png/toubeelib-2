<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use toubeelib_patient\application\actions\GetPatientAction;
use toubeelib_patient\application\actions\PostPatientAction;

return function( App $app): App {


    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    //patients

    $app->post('/patient[/]', PostPatientAction::class)
        ->setName('patientAdd');

    $app->get('/patient/{ID-PATIENT}[/]', GetPatientAction::class)
      ->setName('patientGetById');



    return $app;
};