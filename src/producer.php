<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'mario', 'mario');
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