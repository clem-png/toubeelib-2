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
    $msgJson = json_decode($msg->body, true);
    print "[x] message reçu : " . $msgJson . "\n";
    print_r($msgJson);

    $corpsMail = "<p> date de debut : ". $msgJson['rdv']['dateDebut'] ."</p> <p> Rendez vous de : ". $msgJson['rdv']['specialite_label'] ."</p> <p> De type : ". $msgJson['rdv']['type'] ."</p>";
    $corpsMailPatient = $corpsMail . "<p> Votre praticien sera : ". $msgJson['praticien']['nom'] . " " . $msgJson['praticien']['prenom']."</p>";

    try {
        $mail = new MailEnvoi();
        //envoie patient
        $mail->envoi(getenv('DNS'),'toubelib@mail.com',$msgJson['patient']['mail'],$msgJson['action'],$corpsMailPatient);
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