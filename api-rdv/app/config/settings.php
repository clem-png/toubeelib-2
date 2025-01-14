<?php

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

return  [

    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'logs.name' => 'toubeelib.log',
    'logs.level' => Level::Info,

    'logger' => function( ContainerInterface $c) {
        $log = new Logger( $c->get('logs.name'));
        $log->pushHandler(
            new StreamHandler($c->get('logs.dir'),
                $c->get('logs.level')));
        return $log;
    },
];