<?php
// trvala frona s potvrzenyma msgs
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.99.100', 32771, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('task_queue', false, true, false, false); // trvala fronta

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
  echo " [x] Received ", $msg->body, "\n";
  sleep(substr_count($msg->body, '.')); / number of dots means time is s
  echo " [x] Done", "\n";
  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null); // 1 is 1 message
$channel->basic_consume('task_queue', '', false, false, false, false, $callback); // 4th msg is confirmed

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>