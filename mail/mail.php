<?php

use mail\mailEnvoi;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once 'vendor/autoload.php';

$connection = new AMQPStreamConnection(
    'rabbitmq',
    5672,
    'admin',
    '@dm1#!'
);

$channel = $connection->channel();

$channel->exchange_declare('rdv', 'direct', false, true, false);
$channel->queue_declare('rdv', false, true, false, false);
$channel->queue_bind('rdv', 'rdv', 'rdv');

$queue = 'rdv';

$host = getenv('HOST');
$port = getenv('PORT');
$user = getenv('USER');
$password = getenv('PASSWORD');

$connection = new AMQPStreamConnection($host, $port, $user, $password);
$channel = $connection->channel();
$callback = function(AMQPMessage $msg) {
    $msg_body = json_decode($msg->body, true);
    print "[x] message reçu : " . $msg->body . "\n";

    try {
        $mail = new MailEnvoi();
        $mail->envoi(getenv('DNS'),'hello@example.com','you@example.com','sujet','ça marche');
    }catch (Exception $e){
        print $e->getMessage();
    }
    $msg->getChannel()->basic_ack($msg->getDeliveryTag());

};
$channel->basic_consume($queue, '',false,false,false,false, $callback );
try {
    $channel->consume();
} catch (Exception $e) {
    print $e->getMessage();
}
$channel->close(); $connection->close();