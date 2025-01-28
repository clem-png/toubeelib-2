<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

require_once 'vendor/autoload.php';

$connection = new AMQPStreamConnection('rabbitmq',5672,'admin','@dm1#!');
$channel = $connection->channel();
$msg_body = [
    "caca" => "capitaliste",
    "coco" => "communiste"
];
$msg = new AMQPMessage(json_encode($msg_body)) ;
$channel->basic_publish($msg, 'rdv', "rdv");
print "[x] commande publiée : \n";
$channel->close();
$connection->close();

$queue = 'rdv';

$host = getenv('HOST');
$port = getenv('PORT');
$user = getenv('USER');
$password = getenv('PASSWORD');

$connection = new AMQPStreamConnection($host, $port, $user, $password);
$channel = $connection->channel();
$callback = function(AMQPMessage $msg) {
    $msg_body = json_decode($msg->body, true);
    print "[x] message reçu : \n";

    $transport = Transport::fromDsn(getenv('DNS'));
    $mailer = new Mailer($transport);
    $email = (new Email())
        ->from('hello@example.com')
        ->to('you@example.com')
        ->subject('sujet')
        ->html('<p>ça marche</p>');

    $mailer->send($email);

    $msg->getChannel()->basic_ack($msg->getDeliveryTag());

};
$channel->basic_consume($queue, '',false,false,false,false, $callback );
try {
    $channel->consume();
} catch (Exception $e) {
    print $e->getMessage();
}
$channel->close(); $connection->close();