<?php
declare(strict_types=1);

use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\GetRdvsByIdAction;
use toubeelib\application\actions\PostRdvsAction;
use toubeelib\application\actions\PatchRdvsPatientAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', HomeAction::class);

    $app->put('/rdvs/{ID-RDV}/annuler', PutRdvsAnnulerAction::class)->setName('rdvsAnnuler');

    $app->get('/rdvs/{ID-RDV}[/]', GetRdvsByIdAction::class)->setName('rdvsId');

    $app->post('/rdvs[/]', PostRdvsAction::class)->setName('rdvsAdd');

    $app->patch('/rdvs/{ID-RDV}[/]', PatchRdvsPatientAction::class)->setName('rdvsEditPatient');

    return $app;
};