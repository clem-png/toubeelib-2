<?php

use Psr\Container\ContainerInterface;
use toubeelib\application\actions\GetPraticiensByIdAction;
use toubeelib\application\actions\GetPraticiensDisponibilitesAction;
use toubeelib\application\actions\GetRdvsByIdAction;
use toubeelib\application\actions\PatchRdvsPatientAction;
use toubeelib\application\actions\PostPatientAction;
use toubeelib\application\actions\PostPraticiensAction;
use toubeelib\application\actions\PostRdvsAction;
use toubeelib\application\actions\PutPayerRdvsAction;
use toubeelib\application\actions\PutRdvsAnnulerAction;
use toubeelib\application\actions\PutRdvsHonorerAction;
use toubeelib\application\actions\PutRdvsnonHonorerAction;
use toubeelib\application\actions\SignInAction;
use toubeelib\application\middlewares\AuthorisationMiddleware;
use toubeelib\application\providers\auth\AuthProvider;
use toubeelib\application\providers\auth\AuthProviderInterface;
use toubeelib\application\providers\auth\JWTManager;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\authorization\AuthorizationService;
use toubeelib\core\services\authorization\AuthzPraticienServiceInterface;
use toubeelib\core\services\patient\ServicePatient;
use toubeelib\core\services\patient\ServicePatientInterface;
use toubeelib\core\services\auth\AuthService;
use toubeelib\core\services\auth\AuthServiceInterface;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\core\services\rdv\ServiceRDVInterface;
use toubeelib\infrastructure\repositories\PDOPatientRepository;
use toubeelib\infrastructure\repositories\PDOAuthRepository;
use toubeelib\infrastructure\repositories\PDOPraticienRepository;
use toubeelib\infrastructure\repositories\PDORdvRepository;
use toubeelib\application\actions\PostPraticiensIndisponibiliteAction;

return [

    RdvRepositoryInterface::class => function (ContainerInterface $c){
        return new PDORdvRepository($c->get('rdv.pdo'));
    },

    PraticienRepositoryInterface::class => function (ContainerInterface $c){
        return new PDOPraticienRepository($c->get('praticien.pdo'));
    },

    PatientRepositoryInterface::class => function (ContainerInterface $c){
        return new PDOPatientRepository($c->get('patient.pdo'));
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class),$c->get('logger'));

    },

    ServiceRDVInterface::class => function (ContainerInterface $c) {
        return new ServiceRdv($c->get(RdvRepositoryInterface::class),$c->get(ServicePraticienInterface::class),$c->get('logger'));
    },

    ServicePatientInterface::class => function (ContainerInterface $c) {
        return new ServicePatient($c->get(PatientRepositoryInterface::class),$c->get('logger'));
    },

    AuthzPraticienServiceInterface::class => function (ContainerInterface $c) {
        return new AuthorizationService($c->get(PraticienRepositoryInterface::class));
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

    PostPatientAction::class => function(ContainerInterface $c){
        return new PostPatientAction($c->get(ServicePatientInterface::class));
    },

    GetPraticiensByIdAction::class => function(ContainerInterface $c){
        return new GetPraticiensByIdAction($c->get(ServicePraticienInterface::class));
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

    PostPraticiensAction::class => function(ContainerInterface $c){
        return new PostPraticiensAction($c->get(ServicePraticienInterface::class));
    },

    PostPraticiensIndisponibiliteAction::class => function(ContainerInterface $c){
        return new PostPraticiensIndisponibiliteAction($c->get(ServiceRDVInterface::class));
    },


    //pour jwt

    PDOAuthRepository::class => function(ContainerInterface $c){
        return new PDOAuthRepository($c->get('auth.pdo'));
    },

    AuthServiceInterface::class => function(ContainerInterface $c){
        return new AuthService($c->get(PDOAuthRepository::class),$c->get('logger'));
    },

    JWTManager::class => function(ContainerInterface $c){
        return new JWTManager($c->get('SECRET_KEY'));
    },

    AuthProviderInterface::class => function(ContainerInterface $c){
        return new AuthProvider($c->get(AuthServiceInterface::class),$c->get(JWTManager::class));
    },

    SignInAction::class => function(ContainerInterface $c){
        return new SignInAction($c->get(AuthProviderInterface::class));
    },

    AuthorisationMiddleware::class => function(ContainerInterface $c){
        return new AuthorisationMiddleware($c->get(AuthzPraticienServiceInterface::class));
    }
];