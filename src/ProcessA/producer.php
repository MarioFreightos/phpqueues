<?php


require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

if (isset($_POST)) {

    $frontend_message = $_POST['message'];
    $airline_id = $_POST['airline_id'];
    $string_data = $_POST['file_data'];
    $exchange = 'producerA';
    $routing_key = 'rpc_queue_A';
    $corr_id = uniqid();
    $response = null;

    $channel->exchange_declare(
        $exchange,
        'topic',
        false,
        false,
        false
    );

    list($callback_queue, ,) = $channel->queue_declare(
        "",
        false,
        false,
        true,
        false
    );

    $channel->basic_consume(
        $callback_queue,
        '',
        false,
        true,
        false,
        false,
        function ($rep) {
            global $response;
            global $corr_id;
            if ($rep->get('correlation_id') == $corr_id) {
                $response = $rep->body;
            }
        }
    );

    $msg = new AMQPMessage(
        (string)$airline_id . "_" . $string_data,
        ['correlation_id_A' => $corr_id, 'reply_to_A' => $callback_queue]
    );

    $channel->basic_publish($msg, $exchange, $routing_key);

    while (!$response) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();

    echo ' [.] Got ', $response, "\n";
    die();
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