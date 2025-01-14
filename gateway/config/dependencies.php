<?php

use gateway\application\actions\GeneriquePraticienAction;
use gateway\application\actions\GeneriqueRDVAction;
use gateway\application\actions\GeneriqueUsersAction;
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
        return new Client(['base_uri' => '']);
    },

    'client_auth' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'http://api.toubeelib:80']);
    },

    GeneriquePraticienAction::class => function (ContainerInterface $c){
        return new GeneriquePraticienAction($c->get('client_praticien'));
    },

    GeneriqueRDVAction::class => function (ContainerInterface $c){
        return new GeneriqueRDVAction($c->get('client_rdv'));
    },

    GeneriqueUsersAction::class => function (ContainerInterface $c){
        return new GeneriqueUsersAction($c->get('client_auth'));
    }

];