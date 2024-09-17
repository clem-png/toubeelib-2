<?php

use Psr\Container\ContainerInterface;

return [

    'program.infra.rdv' => function (ContainerInterface $c){
        return new \toubeelib\infrastructure\repositories\ArrayRdvRepository();
    },

    'program.infra.praticien' => function (ContainerInterface $c){
        return new \toubeelib\infrastructure\repositories\ArrayPraticienRepository();
    },


    \toubeelib\core\services\rdv\ServiceRDVInterface::class => function (ContainerInterface $c) {
        return new \toubeelib\core\services\rdv\ServiceRdv($c->get('program.infra.rdv'));
    },

    \toubeelib\core\services\praticien\ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new \toubeelib\core\services\praticien\ServicePraticien($c->get('program.infra.praticien'));

    },

    \toubeelib\application\actions\GetRdvsByIdAction::class => function(ContainerInterface $c){
        return new \toubeelib\application\actions\GetRdvsByIdAction($c->get(\toubeelib\core\services\rdv\ServiceRDVInterface::class));
    }



];