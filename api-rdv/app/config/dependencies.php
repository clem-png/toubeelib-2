<?php

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

use toubeelib\application\actions\GetPraticiensDisponibilitesAction;
use toubeelib\application\actions\GetPraticiensPlanningAction;
use toubeelib\application\actions\GetRdvsByIdAction;
use toubeelib\application\actions\PostPraticiensAction;
use toubeelib\application\actions\PostRdvsAction;
use toubeelib\application\actions\PatchRdvsPatientAction;
use toubeelib\application\actions\PutPayerRdvsAction;
use toubeelib\application\actions\PutRdvsAnnulerAction;
use toubeelib\application\actions\PutRdvsHonorerAction;
use toubeelib\application\actions\PutRdvsnonHonorerAction;
use toubeelib\application\actions\PostPraticiensIndisponibiliteAction;

use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;

use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\core\services\rdv\ServiceRDVInterface;

use toubeelib\infrastructure\repositories\PDORdvRepository;

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
];