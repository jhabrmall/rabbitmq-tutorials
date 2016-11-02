<?php
// consumer
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.99.100', 32771, 'guest', 'guest');
$channel = $connection->channel();


$channel->queue_declare('hello', false, false, false, false); // 3rd param is durable or not

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg) {
  echo " [x] Received ", $msg->body, "\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);
// udrzuje spojeni
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>