<?php

require_once __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory as LoopFactory;
use React\Http\Response;
use React\Http\Server;
use Clue\React\SQLite\Factory as SQLiteFactory;
use React\Socket\Server as Socket;
use Clue\React\SQLite\Result as SQLiteResult;

$loop = LoopFactory::create();

$hello = static function() {
    return new Response(200, [
        'Content-type' => 'text/plain',
        'X-Powered-By' => 'Yo-ho-ho',
    ], 'Hello');
};

$dbFactory = new SQLiteFactory($loop);
$db = $dbFactory->openLazy(__DIR__ . '/storage/database.db');

$listUsers = static function() use ($db) {
    return $db->query('SELECT id, name, email FROM users ORDER BY id')
        ->then(static function(SQLiteResult $result) {
            $users = json_encode($result->rows);

            return new Response(200, [
                'Content-type' => 'application/json',
                'X-Powered-By' => 'Yo-ho-ho',
            ], $users);
        });
};

$server = new Server($listUsers);
$socket = new Socket('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ', str_replace('tcp:', 'http:', $socket->getAddress()), PHP_EOL;
$loop->run();
