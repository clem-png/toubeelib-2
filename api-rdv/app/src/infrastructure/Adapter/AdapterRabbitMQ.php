<?php

namespace toubeelib_rdv\infrastructure\Adapter;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use toubeelib_rdv\application\AdapterInterface\AdapterBrokerInterface;

class AdapterRabbitMQ implements AdapterBrokerInterface
{

    public function publish($message, $routingKey)
    {
        $connection = new AMQPStreamConnection('localhost',5672,'admin','@dm1#!');
        $channel = $connection->channel();
        $msg_body = $message ;
        $msg = new AMQPMessage(json_encode($msg_body)) ;
        $channel->basic_publish($msg, 'rdv', $routingKey);
        print "[x] commande publiée : \n";
        $channel->close();
        $connection->close();
    }
}