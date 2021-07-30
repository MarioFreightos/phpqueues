<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../db_connect.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connection
$db = new DbConnect();
$conn = $db->connect();
$connection = new AMQPStreamConnection(
    'rabbitmq',
    5672,
    'guest',
    'guest'
);

// Channel 1
$channel = $connection->channel();
$exchange = 'producerB';
$routing_key = 'rpc_queue_B';
$channel->exchange_declare(
    $exchange,
    'topic',
    false,
    false,
    false
);
list($queue_name, ,) = $channel->queue_declare(
    "",
    false,
    false,
    true,
    false
);
$channel->queue_bind($queue_name, $exchange, $routing_key);

echo " [x] Todo bien hasta akí! \n";

$my_callback = function ($req) {
    $msg = new AMQPMessage(
        ' ---- correctly recieved AGAIN!',
        array('correlation_id' => $req->get('correlation_id'))
    );
    sleep(3);
    echo " [.] Sending response...\n";
    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    $req->ack();
};

$channel->basic_consume(
    $queue_name,
    '',
    false,
    false,
    false,
    false,
    $my_callback
);

while ($channel->is_open()) {
    $channel->wait();
    echo " [x] Ahoooora, vamo pa lante!!\n";
}

$channel->close();
$connection->close();