<?php

use Psr\Container\ContainerInterface;
use toubeelib_patient\application\actions\PostPatientAction;
use toubeelib_patient\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib_patient\core\services\patient\ServicePatient;
use toubeelib_patient\core\services\patient\ServicePatientInterface;
use toubeelib_patient\infrastructure\repositories\PDOPatientRepository;
use toubeelib_patient\application\actions\GetPatientAction;

return [


    PatientRepositoryInterface::class => function (ContainerInterface $c){
        return new PDOPatientRepository($c->get('patient.pdo'));
    },

    ServicePatientInterface::class => function (ContainerInterface $c) {
        return new ServicePatient($c->get(PatientRepositoryInterface::class));
    },

    PostPatientAction::class => function(ContainerInterface $c){
        return new PostPatientAction($c->get(ServicePatientInterface::class));
    },

    GetPatientAction::class => function(ContainerInterface $c) {
      return new GetPatientAction($c->get(ServicePatient::class));
    }
];