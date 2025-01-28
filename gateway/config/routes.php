<?php
declare(strict_types=1);

use gateway\application\actions\GeneriquePatientAction;
use gateway\application\actions\GeneriquePraticienAction;
use gateway\application\actions\GeneriqueRDVAction;
use gateway\application\actions\GeneriqueUsersAction;
use gateway\application\middleware\AuthMiddleware;
use gateway\application\middleware\Cors;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;


return function( App $app): App {

    $app->add(Cors::class);

    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    /*************************
    * Routes de l'API Praticien
    *************************/
    $app->get('/praticiens[/]', GeneriquePraticienAction::class)
        ->setName('praticiens');

    $app->get('/praticiens/{id}[/]', GeneriquePraticienAction::class)
        ->setName('praticiens-id');

    $app->get('/specialites/{id}[/]', GeneriquePraticienAction::class)
        ->setName('specialites-id');

    
    /*************************
     * Routes de l'API RDV
     *************************/

    $app->put('/rdvs/{ID-RDV}/annuler[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsAnnuler');

    $app->get('/rdvs/{ID-RDV}[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsId');

    $app->post('/rdvs[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsAdd');

    $app->patch('/rdvs/{ID-RDV}[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsEditPatient');


    $app->put('/rdvs/{ID-RDV}/payer[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsPayer');


    $app->put('/rdvs/{ID-RDV}/honorer[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsHonorer');


    $app->put('/rdvs/{ID-RDV}/non-honorer[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('rdvsNonHonorer');

    $app->post('/praticiens/{ID-PRATICIEN}/disponibilites', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('praticiensDispo');


    $app->post('/praticiens/{ID-PRATICIEN}/planning', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('praticiensPlanning');

    $app->post('/praticiens/{ID-PRATICIEN}/indisponibilite[/]', GeneriqueRDVAction::class)
        ->add(AuthMiddleware::class)
        ->setName('praticiensIndispo');

    /*************************
     * Routes de l'API Auth
     *************************/

    $app->post('/users/signin[/]', GeneriqueUsersAction::class)
        ->setName('usersSignIn');

    $app->post('/users/register[/]', GeneriqueUsersAction::class)
        ->setName('usersRegister');

    $app->post('/users/refresh[/]', GeneriqueUsersAction::class)
        ->add(AuthMiddleware::class)
        ->setName('usersRefresh');

  /*************************
   * Routes de l'API Patient
   *************************/

  $app->post('/patient[/]', GeneriquePatientAction::class)
    ->setName('patientPost');

  $app->get('/patient/{ID-PATIENT}[/]', GeneriquePatientAction::class)
    ->setName('patientGetById');


    return $app;
};