<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once "db_connect.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$db = new DbConnect();
$conn = $db->connect();

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$exchange = 'logs';
$routing_key = 'rpc_queue';

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
    echo " [x] Preparing data \n";
    $message = $req->body;
    echo " [.] Message received" . $message . "\n";
    $msg = new AMQPMessage(
        ' ---- correctly recieved!',
        array('correlation_id' => $req->get('correlation_id'))
    );
    sleep(3);
    echo "<pre>";
    var_dump($req);
    echo "\n";
    echo " [.] Sending response...\n";
    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    $req->ack();
};

$channel->basic_qos(null, 1, null);
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
    echo " [x] Pos vale...!\n";
}

$channel->close();
$connection->close();


function placeholders($text, $count = 0, $separator = ",")
{
    $result = array();
    if ($count > 0) {
        for ($x = 0; $x < $count; $x++) {
            $result[] = $text;
        }
    }
    return implode($separator, $result);
}

function multiInsert($data, $datafields)
{
    global $conn;
    // $conn->beginTransaction(); // also helps speed up your inserts.
    $insert_values = array();

    foreach ($data as $d) {
        $question_marks[] = '(' . placeholders('?', count($d)) . ')';
        $insert_values = array_merge($insert_values, array_values($d));
    }

    $sql = "INSERT INTO rates (" . implode(",", $datafields) . ") VALUES " .
        implode(',', $question_marks);

    /*
        echo implode(",", $datafields) . "\n";
        echo implode(',', $question_marks) . "\n";
        echo $sql . "\n";
        var_dump($data);
    */
    $query = $conn->prepare($sql);
    $query->execute($insert_values);
    // $conn->commit();
}
