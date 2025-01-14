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


return function( App $app): App {

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
    $app->post('/praticiens/{ID-PRATICIEN}/disponibilites', GetPraticiensDisponibilitesAction::class)
        ->setName('praticiensDispo');


    $app->post('/praticiens/{ID-PRATICIEN}/planning', GetPraticiensPlanningAction::class)
        ->setName('praticiensPlanning');

    $app->post('/praticiens/{ID-PRATICIEN}/indisponibilite[/]', PostPraticiensIndisponibiliteAction::class)
        ->setName('praticiensIndispo');

    return $app;
};