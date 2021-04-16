<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once "db_connect.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/////// Database Connection ///////
$db = new DbConnect();
$conn = $db->connect();


$connection = new AMQPStreamConnection('rabbitmq', 5672, 'mario', 'mario');
$channel = $connection->channel();
$channel2 = $connection->channel();

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

list($queue_name, ,) = $channel->queue_declare(
    "",
    false,
    false,
    true,
    false);
list($queue_name2, ,) = $channel2->queue_declare(
    "",
    false,
    false,
    true,
    false);

$channel->queue_bind($queue_name, 'logs', 'routing_key_log');
$channel2->queue_bind($queue_name2, 'otroexchange', 'otroexchange_log');

echo " [x] Todo bien hasta akí! \n";

$mi_funcion = function ($msg) {
    echo " [x] Preparing data \n";

    $all_data = explode('_', $msg->body);
    $airline_id = $all_data[0];
    $array_data = json_decode($all_data[1]);
    $data_fields = $array_data[0]; // Getting headers into variable

    array_push($data_fields, 'sync'); // Insert a last column
    array_shift($array_data); // Removing headers from data array

    echo " [x] Working for airline_id: " . $airline_id . "\n";

    $my_data = array();

    foreach ($array_data as $data_row) {
        $new_array = array();
        $new_array['airline_id'] = $airline_id; // First column
        foreach ($data_row as $data_row_key => $data_row_value) {
            $new_array[$data_fields[$data_row_key]] = (float)$data_row_value;
        }
        $new_array['sync'] = 1; // Last column
        $my_data[] = $new_array;
    }

    echo " [x] Inserting data \n";

    array_unshift($data_fields, 'airline_id');
    multiInsert($my_data, $data_fields);

    echo " [x] Data inserted \n";
};

$channel->basic_consume(
    $queue_name,
    '',
    false,
    false,
    false,
    false,
    $mi_funcion
);
$channel2->basic_consume(
    $queue_name2,
    '',
    false,
    false,
    false,
    false,
    function($msg){
        echo " [x] Canal 2". $msg->body;
    }
);

while ($channel->is_consuming() && $channel2->is_consuming()) {
    $channel->wait();
    echo " [x] Esperando al otro canal...\n";
    $channel2->wait();
    echo " [x] Perfecto!\n";
}


$channel->close();
$channel2->close();
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
