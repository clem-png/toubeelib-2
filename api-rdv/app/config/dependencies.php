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

use toubeelib_rdv\infrastructure\repositories\PDORdvRepository;

return [

    'client_praticien' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://gateway.toubeelib:80']);
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
    }
];