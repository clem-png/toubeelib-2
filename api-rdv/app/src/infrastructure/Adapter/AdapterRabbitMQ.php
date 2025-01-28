<?php

namespace toubeelib_rdv\infrastructure\Adapter;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use toubeelib_rdv\application\AdapterInterface\AdapterBrokerInterface;

class AdapterRabbitMQ implements AdapterBrokerInterface
{
    private AMQPChannel $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function publish($message, $routingKey)
    {
        try {
            $msg_body = $message;
            $msg = new AMQPMessage(json_encode($msg_body));
            $this->channel->basic_publish($msg, 'rdv', $routingKey);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}