<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel2 = $connection->channel();

if (isset($_POST)) {

    $frontend_message = $_POST['message'];
    $airline_id = $_POST['airline_id'];
    $string_data = $_POST['file_data'];

    $channel->exchange_declare(
        'logs',
        'topic',
        false,
        false,
        false
    );

    $channel2->exchange_declare(
        'otroexchange',
        'topic',
        false,
        false,
        false
    );

    $msg = new AMQPMessage($airline_id . "_" . $string_data);

    $channel->basic_publish($msg, 'logs', 'routing_key_log');
    $channel2->basic_publish($msg,'otroexchange','otroexchange_log');

    $channel->close();
    $connection->close();

    die($airline_id . "_" . $string_data);
}
/*$connection = new AMQPStreamConnection(
    'amqps://b-472341bf-4b47-438b-945b-70d1823221a9.mq.eu-west-1.amazonaws.com',
    5671,
    'spiderweb',
    'wcn8765-wcn4321'
);

$channel = $connection->channel();

if (isset($_POST)) {

    $msg = new AMQPMessage('Test message');

    $channel->basic_publish($msg, '', 'logs');
    $channel->close();
    $connection->close();

    die('Test message sent correctly');
}*/