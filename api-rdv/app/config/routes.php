<?php
declare(strict_types=1);

use Slim\App;
use toubeelib_rdv\application\actions\GetPraticiensDisponibilitesAction;
use toubeelib_rdv\application\actions\GetPraticiensPlanningAction;
use toubeelib_rdv\application\actions\GetRdvsByIdAction;
use toubeelib_rdv\application\actions\PostPraticiensAction;
use toubeelib_rdv\application\actions\PostRdvsAction;
use toubeelib_rdv\application\actions\PatchRdvsPatientAction;
use toubeelib_rdv\application\actions\PutPayerRdvsAction;
use toubeelib_rdv\application\actions\PutRdvsAnnulerAction;
use toubeelib_rdv\application\actions\PutRdvsHonorerAction;
use toubeelib_rdv\application\actions\PutRdvsnonHonorerAction;
use toubeelib_rdv\application\actions\PostPraticiensIndisponibiliteAction;

use toubeelib_rdv\application\middlewares\AuthorisationMiddleware;

return function( App $app): App {

    //rdvs
    $app->put('/rdvs/{id}/annuler[/]', PutRdvsAnnulerAction::class)
        ->setName('rdvsAnnuler');

    $app->get('/rdvs/{id}[/]', GetRdvsByIdAction::class)
        ->add(AuthorisationMiddleware::class)
        ->setName('rdvsId');

    $app->post('/rdvs[/]', PostRdvsAction::class)
        ->add(AuthorisationMiddleware::class)    
        ->setName('rdvsAdd');

    $app->patch('/rdvs/{id}[/]', PatchRdvsPatientAction::class)
        ->setName('rdvsEditPatient');


    $app->put('/rdvs/{id}/payer[/]', PutPayerRdvsAction::class)
        ->setName('rdvsPayer');


    $app->put('/rdvs/{id}/honorer[/]', PutRdvsHonorerAction::class)
        ->setName('rdvsHonorer');


    $app->put('/rdvs/{id}/non-honorer[/]', PutRdvsNonHonorerAction::class)
        ->setName('rdvsNonHonorer');


    //praticiens
    $app->post('/praticiens/{id}/disponibilites', GetPraticiensDisponibilitesAction::class)
        ->setName('praticiensDispo');


    $app->post('/praticiens/{id}/planning', GetPraticiensPlanningAction::class)
        ->add(AuthorisationMiddleware::class)
        ->setName('praticiensPlanning');

    $app->post('/praticiens/{id}/indisponibilite[/]', PostPraticiensIndisponibiliteAction::class)
        ->setName('praticiensIndispo');

    return $app;
};