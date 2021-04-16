<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

if (isset($_POST)) {
    $exchange_name = 'logs';
    $airline_id = (int)$_POST['airline_id'];
    $string_data = $_POST['file_data'];

    $channel->exchange_declare(
        $exchange_name,
        'topic',
        false,
        false,
        false
    );
    $routing_key = 'routing_sync_log';
    $msg = new AMQPMessage($airline_id . "_" . $string_data);
    $channel->basic_publish($msg, $exchange_name, $routing_key);
    $channel->close();
    $connection->close();
    die('Data sent correctly');
}

?>