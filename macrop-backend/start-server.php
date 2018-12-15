<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Pusher;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server;
use React\ZMQ\Context;
use React\EventLoop\Factory;

$loop   = Factory::create();
$pusher = new Pusher;

// Listen for the web server to make a ZeroMQ push after an ajax request
$context = new Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://0.0.0.0:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
$pull->on('message', array($pusher, 'onMessage'));

// Set up our WebSocket server for clients wanting real-time updates
$webSock = new Server('0.0.0.0:8090', $loop); // Binding to 0.0.0.0 means remotes can connect
$webServer = new IoServer(
    new HttpServer(
        new WsServer(
                $pusher
        )
    ),
    $webSock
);

$loop->run();