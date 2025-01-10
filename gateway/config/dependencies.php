<?php

use gateway\application\actions\GeneriquePraticienAction;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
return [

    'client' => function (ContainerInterface $c){
        return new Client(['base_uri' => 'localhost:6080']);
    },

    'client_praticien' => function (ContainerInterface $c){
        return new Client(['base_uri' => '']);
    },

    'client_rdv' => function (ContainerInterface $c){
        return new Client(['base_uri' => '']);
    },

    'client_patient' => function (ContainerInterface $c){
        return new Client(['base_uri' => '']);
    },

    GeneriquePraticienAction::class => function (ContainerInterface $c){
        return new GeneriquePraticienAction($c->get('client_praticien'));
    }

];