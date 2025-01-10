<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use toubeelib\application\actions\GetPraticiensAction;
use toubeelib\application\actions\GetPraticiensByIdAction;
use toubeelib\application\actions\GetPraticiensDisponibilitesAction;
use toubeelib\application\actions\GetPraticiensPlanningAction;
use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\GetRdvsByIdAction;
use toubeelib\application\actions\PostPraticiensAction;
use toubeelib\application\actions\PostRdvsAction;
use toubeelib\application\actions\PatchRdvsPatientAction;
use toubeelib\application\actions\PostPatientAction;
use toubeelib\application\actions\PutPayerRdvsAction;
use toubeelib\application\actions\PutRdvsAnnulerAction;
use toubeelib\application\actions\PutRdvsHonorerAction;
use toubeelib\application\actions\PutRdvsnonHonorerAction;
use toubeelib\application\actions\PostPraticiensIndisponibiliteAction;
use toubeelib\application\actions\SignInAction;
use toubeelib\application\middlewares\AuthMiddleware;
use toubeelib\application\middlewares\AuthorisationMiddleware;
use toubeelib\application\middlewares\Cors;

return function( App $app): App {

    $app->add(Cors::class);

    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    $app->get('/', HomeAction::class);

    //rdvs

    $app->put('/rdvs/{ID-RDV}/annuler[/]', PutRdvsAnnulerAction::class)
        ->setName('rdvsAnnuler');


    $app->get('/rdvs/{ID-RDV}[/]', GetRdvsByIdAction::class)
        ->setName('rdvsId');


    $app->post('/rdvs[/]', PostRdvsAction::class)
        ->setName('rdvsAdd');


    $app->patch('/rdvs/{ID-RDV}[/]', PatchRdvsPatientAction::class)
        ->setName('rdvsEditPatient');


    $app->put('/rdvs/{ID-RDV}/payer[/]', PutPayerRdvsAction::class)
        ->setName('rdvsPayer');


    $app->put('/rdvs/{ID-RDV}/honorer[/]', PutRdvsHonorerAction::class)
        ->setName('rdvsHonorer');


    $app->put('/rdvs/{ID-RDV}/non-honorer[/]', PutRdvsNonHonorerAction::class)
        ->setName('rdvsNonHonorer');


    //praticiens

    $app->get('/praticiens[/]', GetPraticiensAction::class)
        ->setName('praticiens');



    $app->get('/praticiens/{ID-PRATICIEN}/disponibilites', GetPraticiensDisponibilitesAction::class)
        ->setName('praticiensDispo');


    $app->get('/praticiens/{ID-PRATICIEN}/planning', GetPraticiensPlanningAction::class)
        ->setName('praticiensPlanning');


    $app->get('/praticiens/{ID-PRATICIEN}[/]', GetPraticiensByIdAction::class)
        ->setName('praticiensId');



    $app->post('/praticiens/{ID-PRATICIEN}/indisponibilite[/]', PostPraticiensIndisponibiliteAction::class)
        ->setName('praticiensIndispo');




    $app->post('/praticiens[/]', PostPraticiensAction::class)
        ->setName('praticiensAdd');



    //patients

    $app->post('/patient[/]', PostPatientAction::class)
        ->setName('patientAdd');


    //users

    $app->post('/users/signin[/]', SignInAction::class)
        ->setName('usersSignIn');

    return $app;
};