<?php

use gateway\application\actions\GeneriquePatientAction;
use gateway\application\actions\GeneriquePraticienAction;
use gateway\application\actions\GeneriqueRDVAction;
use gateway\application\actions\GeneriqueUsersAction;
use gateway\application\middleware\AuthMiddleware;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
return [

    'client_praticien' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://api.praticiens.toubeelib:80']);
    },

    'client_rdv' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://api.rdv.toubeelib:80']);
    },

    'client_patient' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://api.patient.toubeelib:80']);
    },

    'client_auth' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://api.auth.toubeelib:80']);
    },

    GeneriquePraticienAction::class => function (ContainerInterface $c){
        return new GeneriquePraticienAction($c->get('client_praticien'));
    },

    GeneriqueRDVAction::class => function (ContainerInterface $c){
        return new GeneriqueRDVAction($c->get('client_rdv'));
    },

    GeneriqueUsersAction::class => function (ContainerInterface $c){
        return new GeneriqueUsersAction($c->get('client_auth'));
    },

    GeneriquePatientAction::class => function (ContainerInterface $c){
      return new GeneriquePatientAction($c->get('client_patient'));
    },

    AuthMiddleware::class => function (ContainerInterface $c) {
        return new AuthMiddleware($c->get('client_auth'));
    },


];