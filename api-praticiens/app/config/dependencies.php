<?php

use Psr\Container\ContainerInterface;
use toubeelib_praticiens\application\actions\GetPraticiensByIdAction;
use toubeelib_praticiens\application\actions\PostPraticiensAction;
use toubeelib_praticiens\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib_praticiens\core\services\praticien\ServicePraticien;
use toubeelib_praticiens\core\services\praticien\ServicePraticienInterface;
use toubeelib_praticiens\infrastructure\repositories\PDOPraticienRepository;
use toubeelib_praticiens\application\actions\GetSpecialiteByIdAction;

return [
    GetSpecialiteByIdAction::class => function (ContainerInterface $c){
        return new GetSpecialiteByIdAction($c->get(ServicePraticienInterface::class));
    },

    PraticienRepositoryInterface::class => function (ContainerInterface $c){
        return new PDOPraticienRepository($c->get('praticien.pdo'));
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class),$c->get('logger'));

    },

    GetPraticiensByIdAction::class => function(ContainerInterface $c){
        return new GetPraticiensByIdAction($c->get(ServicePraticienInterface::class));
    },

    PostPraticiensAction::class => function(ContainerInterface $c){
        return new PostPraticiensAction($c->get(ServicePraticienInterface::class));
    },

];