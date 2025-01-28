<?php

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

return  [

    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'logs.name' => 'toubeelib.log',
    'logs.level' => Level::Info,

    'rdv.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file('iniconf/rdv.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']};";
        $user = $config['username'];
        $password = $config['password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },

    'logger' => function( ContainerInterface $c) {
        $log = new Logger( $c->get('logs.name'));
        $log->pushHandler(
            new StreamHandler($c->get('logs.dir'),
                $c->get('logs.level')));
        return $log;
    },

    'SECRET_KEY' => getenv('lJWT_SECRET_KEY'),

    'channel' => function (ContainerInterface $c) {
    
        $connection = new AMQPStreamConnection(
            'rabbitmq',
            5672,
            'admin',
            '@dm1#!'
        );
        $channel = $connection->channel();
    
        return $channel;
    },
];