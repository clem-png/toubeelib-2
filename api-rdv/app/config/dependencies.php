<?php

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

use toubeelib_rdv\application\actions\GetPraticiensDisponibilitesAction;
use toubeelib_rdv\application\actions\GetPraticiensPlanningAction;
use toubeelib_rdv\application\actions\GetRdvsByIdAction;
use toubeelib_rdv\application\actions\PostRdvsAction;
use toubeelib_rdv\application\actions\PatchRdvsPatientAction;
use toubeelib_rdv\application\actions\PutPayerRdvsAction;
use toubeelib_rdv\application\actions\PutRdvsAnnulerAction;
use toubeelib_rdv\application\actions\PutRdvsHonorerAction;
use toubeelib_rdv\application\actions\PutRdvsnonHonorerAction;
use toubeelib_rdv\application\actions\PostPraticiensIndisponibiliteAction;

use toubeelib_rdv\core\repositoryInterfaces\RdvRepositoryInterface;

use toubeelib_rdv\core\services\rdv\ServiceRdv;
use toubeelib_rdv\core\services\rdv\ServiceRDVInterface;
use toubeelib_rdv\core\services\praticien\ServicePraticienInterface;

use toubeelib_rdv\infrastructure\repositories\PDORdvRepository;
use toubeelib_rdv\infrastructure\repositories\PraticienServiceAdapter;

use toubeelib_rdv\core\services\authorisation\AuthorisationService;
use toubeelib_rdv\core\services\authorisation\AuthorisationServiceInterface;

use toubeelib_rdv\application\middlewares\AuthorisationMiddleware;
use toubeelib_rdv\application\providers\JWTManager;

return [

    'client_praticien' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://api.praticiens.toubeelib:80']);
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new PraticienServiceAdapter($c->get('client_praticien'), $c->get('logger'));
    },

    RdvRepositoryInterface::class => function (ContainerInterface $c){
        return new PDORdvRepository($c->get('rdv.pdo'));
    },

    ServiceRDVInterface::class => function (ContainerInterface $c) {
        return new ServiceRdv($c->get(RdvRepositoryInterface::class),$c->get(ServicePraticienInterface::class),$c->get('logger'));
    },

    GetRdvsByIdAction::class => function(ContainerInterface $c){
        return new GetRdvsByIdAction($c->get(ServiceRDVInterface::class));
    },

    PostRdvsAction::class => function(ContainerInterface $c){
        return new PostRdvsAction($c->get(ServiceRDVInterface::class));
    },

    PutRdvsAnnulerAction::class => function(ContainerInterface $c){
        return new PutRdvsAnnulerAction($c->get(ServiceRDVInterface::class));
    },

    GetPraticiensDisponibilitesAction::class => function(ContainerInterface $c){
        return new GetPraticiensDisponibilitesAction($c->get(ServiceRDVInterface::class));
    },

    PatchRdvsPatientAction::class => function(ContainerInterface $c){
        return new PatchRdvsPatientAction($c->get(ServiceRDVInterface::class));
    },

    PutPayerRdvsAction::class => function(ContainerInterface $c){
        return new PutPayerRdvsAction($c->get(ServiceRDVInterface::class));
    },

    PutRdvsHonorerAction::class => function(ContainerInterface $c){
        return new PutRdvsHonorerAction($c->get(ServiceRDVInterface::class));
    },

    PutRdvsnonHonorerAction::class => function(ContainerInterface $c){
        return new PutRdvsnonHonorerAction($c->get(ServiceRDVInterface::class));
    },

    PostPraticiensIndisponibiliteAction::class => function(ContainerInterface $c){
        return new PostPraticiensIndisponibiliteAction($c->get(ServiceRDVInterface::class));
    },

    GetPraticiensPlanningAction::class => function(ContainerInterface $c){
        return new GetPraticiensPlanningAction($c->get(ServiceRDVInterface::class));
    },

    JWTManager::class => function(ContainerInterface $c){
        return new JWTManager($c->get('SECRET_KEY'));
    },

    AuthorisationServiceInterface::class => function(ContainerInterface $c){
        return new AuthorisationService($c->get(RdvRepositoryInterface::class));
    },

    AuthorisationMiddleware::class => function(ContainerInterface $c){
        return new AuthorisationMiddleware($c->get(AuthorisationServiceInterface::class) ,$c->get(JWTManager::class));
    },
];