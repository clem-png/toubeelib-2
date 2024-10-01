<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\actions\GetPraticiensDisponibilitesAction;
use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\GetRdvsByIdAction;
use toubeelib\application\actions\PostRdvsAction;
use toubeelib\application\actions\PatchRdvsPatientAction;
use toubeelib\application\actions\PutRdvsAnnulerAction;
use toubeelib\application\middlewares\Cors;

return function( \Slim\App $app):\Slim\App {

    $app->add(Cors::class);

    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    $app->get('/', HomeAction::class);

    $app->put('/rdvs/{ID-RDV}/annuler', PutRdvsAnnulerAction::class)->setName('rdvsAnnuler');

    $app->get('/rdvs/{ID-RDV}[/]', GetRdvsByIdAction::class)->setName('rdvsId');

    $app->post('/rdvs[/]', PostRdvsAction::class)->setName('rdvsAdd');

    $app->patch('/rdvs/{ID-RDV}[/]', PatchRdvsPatientAction::class)->setName('rdvsEditPatient');

    $app->get('/praticiens/{ID-PRATICIEN}/disponibilites', GetPraticiensDisponibilitesAction::class)->setName('praticiensDispo');
    return $app;
};